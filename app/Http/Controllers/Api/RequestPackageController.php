<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestNotificationServiceInterface;
use App\Contracts\Services\RequestPackageServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Helpers\Utils;
use App\Http\Requests\Request\StarRatingRequest;
use App\Http\Requests\RequestPackage\ApprovedPackageRequest;
use App\Http\Requests\RequestPackage\StoreRequestPackageRequest;
use App\Http\Requests\RequestPackage\TransferPackageRequest;
use App\Http\Requests\RequestPackage\UploadFileRequestPackageRequest;
use App\Http\Requests\RequestRoom\CancelRequestRoomRequest;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Package\PackageExposedResource;
use App\Http\Resources\Package\PackageResource;
use App\Http\Resources\RequestPackage\RequestPackageViewCollection;
use App\Http\Resources\Util\StartDateEndDateResource;
use App\Models\Enums\NameRole;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpCodes;


class RequestPackageController extends BaseApiController
{
    private $requestPackageService;
    private $notificationServiceInterface;
    private $requestNotificationService;

    public function __construct(RequestPackageServiceInterface $requestPackageService,
                                NotificationServiceInterface $notificationServiceInterface,
                                RequestNotificationServiceInterface $requestNotificationService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'uploadAuthorizationFile');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'show', 'getStatusByStatusCurrent', 'cancelRequest');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('transferRequest', 'getDriverSchedule', 'getPackagesByDriverId', 'onReadRequest');
        
        $this->requestPackageService = $requestPackageService;
        $this->notificationServiceInterface = $notificationServiceInterface;
        $this->requestNotificationService = $requestNotificationService;
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
    public function uploadAuthorizationFile(int $requestId, UploadFileRequestPackageRequest $request): JsonResponse
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
        $package = $this->requestPackageService->findById($requestId);
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
    public function cancelRequest(int $requestId, CancelRequestRoomRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $dto->request_id = $requestId;
        $requestCanceled = $this->requestPackageService->cancelRequest($dto);
        $notificationCancel = $this->notificationServiceInterface
            ->cancelRequestPackageNotification($requestCanceled, auth()->user());
        $this->requestNotificationService->create($requestCanceled->id, $notificationCancel->id);
        Utils::eventAlertNotification($notificationCancel);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function transferRequest(int $packageId, TransferPackageRequest $request): JsonResponse
    {
        $packageTransfer = $this->requestPackageService->transferRequest($packageId, $request->toDTO());
        $packageTransferNotification = $this->notificationServiceInterface
            ->transferPackageRequestNotification($packageTransfer);
        $this->requestNotificationService->create($packageTransfer->request->id, $packageTransferNotification->id);
        Utils::eventAlertNotification($packageTransferNotification);
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
    public function approvedRequestPackage(ApprovedPackageRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $packageApproved = $this->requestPackageService->approvedRequestPackage($dto);
        $packageApprovedNotification = $this->notificationServiceInterface
            ->approvedPackageRequestNotification($packageApproved);
        $this->requestNotificationService->create($packageApproved->request->id, $packageApprovedNotification->id);
        Utils::eventAlertNotification($packageApprovedNotification);
        return $this->noContentResponse();
    }

    public function insertScore(StarRatingRequest $request): JsonResponse
    {
        $scoreDTO = $request->toDTO();
        $packageDelivered = $this->requestPackageService->insertScore($scoreDTO);
        $packeDeliveredNotification = $this->notificationServiceInterface
            ->deliveredPackageRequestNotification($packageDelivered);
        $this->requestNotificationService->create($packageDelivered->id, $packeDeliveredNotification->id);
        Utils::eventAlertNotification($packeDeliveredNotification);
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

    public function showPackage(int $requestId): JsonResponse
    {
        $package = $this->requestPackageService->findByRequestId($requestId);
        return $this->showOne(new PackageExposedResource($package));
    }

    public function onRoadPackage(int $requestId): JsonResponse
    {
        $requestPackageOnRoad = $this->requestPackageService->onRoadPackage($requestId);
        $requestPackageRoadNotification = $this->notificationServiceInterface
            ->onRoadPackageRequestNotification($requestPackageOnRoad);
        $this->requestNotificationService->create($requestPackageOnRoad->id, $requestPackageRoadNotification->id);
        Utils::eventAlertNotification($requestPackageRoadNotification);
        return $this->noContentResponse();
    }
}
