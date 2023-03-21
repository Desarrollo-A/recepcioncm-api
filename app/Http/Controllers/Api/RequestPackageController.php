<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestPackageServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\CancelRequest\CancelRequest;
use App\Http\Requests\DeliveredPackage\DeliveredPackageRequest;
use App\Http\Requests\DeliveredPackage\UploadSignatureRequest;
use App\Http\Requests\Request\ResponseRejectRequest;
use App\Http\Requests\Request\StarRatingRequest;
use App\Http\Requests\RequestPackage\AcceptCancelPackageRequest;
use App\Http\Requests\RequestPackage\ApprovedPackageRequest;
use App\Http\Requests\RequestPackage\ProposalPackageRequest;
use App\Http\Requests\RequestPackage\StoreRequestPackageRequest;
use App\Http\Requests\RequestPackage\TransferPackageRequest;
use App\Http\Requests\RequestPackage\UploadFileRequestPackageRequest;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Package\PackageExposedResource;
use App\Http\Resources\Package\PackageResource;
use App\Http\Resources\RequestPackage\RequestPackageViewCollection;
use App\Http\Resources\Util\StartDateEndDateResource;
use App\Models\Enums\NameRole;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpCodes;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequestPackageController extends BaseApiController
{
    private $requestPackageService;
    private $notificationService;

    public function __construct(RequestPackageServiceInterface $requestPackageService,
                                NotificationServiceInterface $notificationService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'uploadAuthorizationFile', 'responseRejectRequest');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'cancelRequest');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST.','.NameRole::DRIVER)
            ->only('show', 'getStatusByStatusCurrent');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('transferRequest', 'getDriverSchedule', 'getPackagesByDriverId', 'onReadRequest',
                'findAllByDateAndOffice', 'proposalRequest', 'approvedRequest');
        $this->middleware('role.permission:'.NameRole::DRIVER)
            ->only('findAllByDriverIdPaginated', 'onRoad', 'deliveredRequest', 'deliveredRequestSignature', 'findAllDeliveredByDriverIdPaginated', 
                'getRequestPackageReportPdf', 'getRequestPackageReportExcel');
        $this->middleware('role.permission:'.NameRole::DEPARTMENT_MANAGER)
            ->only('acceptCancelPackage');
        
        $this->requestPackageService = $requestPackageService;
        $this->notificationService = $notificationService;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreRequestPackageRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $package = $this->requestPackageService->createRequestPackage($dto);
        return $this->showOne(new PackageResource($package));
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadAuthorizationFile(int $requestId, UploadFileRequestPackageRequest $request): Response
    {
        $dto = $request->toDTO();
        $this->requestPackageService->uploadAuthorizationFile($requestId, $dto);
        return $this->noContentResponse();
    }

    public function index(Request $request): JsonResponse
    {
        $requestPackages = $this->requestPackageService->findAllPackagesPaginated($request, auth()->user());
        return $this->showAll(new RequestPackageViewCollection($requestPackages, true));
    }

    public function show(int $requestId): JsonResponse
    {
        $package = $this->requestPackageService->findByPackageRequestId($requestId, auth()->user());
        return $this->showOne(new PackageResource($package));
    }

    public function getStatusByStatusCurrent(string $code): JsonResponse
    {
        $roleName = auth()->user()->role->name;
        $status = $this->requestPackageService->getStatusByStatusCurrent($code, $roleName);
        return $this->showAll(LookupResource::collection($status));
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequest(int $requestId, CancelRequest $request): Response
    {
        $dto = $request->toDTO();
        $dto->request_id = $requestId;
        $data = $this->requestPackageService->cancelRequest($dto);
        $this->notificationService->cancelRequestPackageNotification($data->request, auth()->user(), $data->driverId);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function transferRequest(int $packageId, TransferPackageRequest $request): Response
    {
        $packageTransfer = $this->requestPackageService->transferRequest($packageId, $request->toDTO());
        $this->notificationService->transferPackageRequestNotification($packageTransfer);
        return $this->noContentResponse();
    }

    public function getDriverSchedule(int $officeId): JsonResponse
    {
        $schedule = $this->requestPackageService->getScheduleDriver($officeId);
        return $this->showAll(StartDateEndDateResource::collection($schedule));
    }

    public function getPackagesByDriverId(int $driverId, string $date): JsonResponse
    {
        $packages = $this->requestPackageService->getPackagesByDriverId($driverId, new Carbon($date));
        return $this->showAll(PackageResource::collection($packages));
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedRequest(ApprovedPackageRequest $request): Response
    {
        $dto = $request->toDTO();
        $packageApproved = $this->requestPackageService->approvedRequest($dto);
        if (isset($dto->driverPackageSchedule->driverSchedule->driver_id)) {
            $this->notificationService->approvedPackageRequestNotification($packageApproved,
                $dto->driverPackageSchedule->driverSchedule->driver_id);
        }
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function insertScore(StarRatingRequest $request): Response
    {
        $scoreDTO = $request->toDTO();
        $this->requestPackageService->insertScore($scoreDTO);
        return $this->noContentResponse();
    }

    public function isPackageCompleted(int $requestPackageId): JsonResponse
    {
        $requests = $this->requestPackageService->isPackageCompleted($requestPackageId);
        return $this->successResponse(['deliveredPackage' => $requests], HttpCodes::HTTP_OK);
    }

    public function isAuthPackage(string $authCodePackage): JsonResponse
    {
        $requestPackageAuthCode = $this->requestPackageService->isAuthPackage($authCodePackage);
        return $this->successResponse(['authCodePackage' => $requestPackageAuthCode], HttpCodes::HTTP_OK);
    }

    public function showExposedPackage(int $requestId): JsonResponse
    {
        $package = $this->requestPackageService->findByRequestId($requestId);
        return $this->showOne(new PackageExposedResource($package));
    }

    public function onRoad(int $requestId): Response
    {
        $requestPackageOnRoad = $this->requestPackageService->onRoad($requestId);
        $this->notificationService->onRoadPackageRequestNotification($requestPackageOnRoad);
        return $this->noContentResponse();
    }

    public function findAllByDateAndOffice(int $office, string $date): JsonResponse
    {
        $packages = $this->requestPackageService->findAllByDateAndOffice($office, new Carbon($date));
        return $this->showAll(PackageResource::collection($packages));
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalRequest(ProposalPackageRequest $request): Response
    {
        $requestPackageProposal = $this->requestPackageService->proposalRequest($request->toDTO());
        $this->notificationService->proposalPackageRequestNotification($requestPackageProposal);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function responseRejectRequest(int $requestId, ResponseRejectRequest $request): Response
    {
        $dto = $request->toDTO();
        $request = $this->requestPackageService->responseRejectRequest($requestId, $dto);
        $this->notificationService->responseRejectRequestNotification($request);
        return $this->noContentResponse();
    }

    public function findAllByDriverIdPaginated(Request $request): JsonResponse
    {
        $requestPackages = $this->requestPackageService->findAllByDriverIdPaginated($request, auth()->user());
        return $this->showAll(new RequestPackageViewCollection($requestPackages, true));
    }

    public function findAllDeliveredByDriverIdPaginated(Request $request): JsonResponse
    {
        $requestPackage = $this->requestPackageService->findAllDeliveredByDriverIdPaginated($request, auth()->user());
        return $this->showAll(new RequestPackageViewCollection($requestPackage, true));
    }

    /**
     * @throws CustomErrorException
     */
    public function deliveredRequest(DeliveredPackageRequest $request): Response
    {
        $dto = $request->toDTO();
        $packageDelivered = $this->requestPackageService->deliveredPackage($dto);
        $this->notificationService->deliveredPackageRequestNotification($packageDelivered);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function deliveredRequestSignature(int $packageId, UploadSignatureRequest $request): Response
    {
        $this->requestPackageService->deliveredRequestSignature($packageId, $request->toDTO());
        return $this->noContentResponse();
    }

    public function getRequestPackageReportPdf(Request $request)
    {
        return $this->requestPackageService->reportRequestPackagePdf($request, auth()->id());
    }

    public function getRequestPackageReportExcel(Request $request): StreamedResponse
    {
        return $this->requestPackageService->reportRequestPackageExcel($request, auth()->id());
    }

    /**
     * @throws CustomErrorException
     */
    public function acceptCancelPackage(int $requestId, AcceptCancelPackageRequest $request): Response
    {
        $package = $this->requestPackageService->acceptCancelPackage($requestId, $request->toDTO());
        $this->notificationService->acceptOrCancelPackageRequestNotification($package);
        return $this->noContentResponse();
    }
}
