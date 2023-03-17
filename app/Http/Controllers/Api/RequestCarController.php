<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestCarServiceInterface;
use App\Contracts\Services\RequestEmailServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\CancelRequest\CancelRequest;
use App\Http\Requests\Request\ResponseRejectRequest;
use App\Http\Requests\RequestCar\AddExtraInformationCarRequest;
use App\Http\Requests\RequestCar\ApprovedCarRequest;
use App\Http\Requests\RequestCar\ProposalCarRequest;
use App\Http\Requests\RequestCar\StoreRequestCarRequest;
use App\Http\Requests\RequestCar\TransferCarRequest;
use App\Http\Requests\RequestCar\UploadZipImagesCarRequest;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\RequestCar\RequestCarResource;
use App\Http\Resources\RequestCar\RequestCarViewCollection;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestCarController extends BaseApiController
{
    private $requestCarService;
    private $notificationService;
    private $requestEmailService;

    public function __construct(RequestCarServiceInterface $requestCarService,
                                NotificationServiceInterface $notificationService,
                                RequestEmailServiceInterface $requestEmailService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'deleteRequestCar', 'responseRejectRequest');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'store', 'show', 'cancelRequest', 'getStatusByStatusCurrent');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('transferRequest', 'approvedRequest', 'getBusyDaysForProposalCalendar', 'proposalRequest',
                'uploadZipImages', 'addExtraCarInformation');

        $this->requestCarService = $requestCarService;
        $this->notificationService = $notificationService;
        $this->requestEmailService = $requestEmailService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $requestCars = $this->requestCarService->findAllCarsPaginated($request, $user);
        return $this->showAll(new RequestCarViewCollection($requestCars, true));
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreRequestCarRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $requestCar = $this->requestCarService->create($dto);
        return $this->showOne(new RequestCarResource($requestCar));
    }

    public function deleteRequestCar(int $requestId): Response
    {
        $requestCar = $this->requestCarService->deleteRequestCar($requestId, auth()->user());
        $this->notificationService->deleteRequestCarNotification($requestCar);
        return $this->noContentResponse();
    }

    public function show(int $requestId): JsonResponse
    {
        $requestCar = $this->requestCarService->findByRequestId($requestId, auth()->user());
        return $this->showOne(new RequestCarResource($requestCar));
    }

    public function getStatusByStatusCurrent(string $code): JsonResponse
    {
        $roleName = auth()->user()->role->name;
        $status = $this->requestCarService->getStatusByStatusCurrent($code, $roleName);
        return $this->showAll(LookupResource::collection($status));
    }

    public function transferRequest(int $requestCarId, TransferCarRequest $request): Response
    {
        $requestCar = $this->requestCarService->transferRequest($requestCarId, $request->toDTO());
        $this->notificationService->transferRequestCarNotification($requestCar);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequest(int $requestId, CancelRequest $request): Response
    {
        $dto = $request->toDTO();
        $dto->request_id = $requestId;
        $data = $this->requestCarService->cancelRequest($dto);
        $this->notificationService->cancelRequestCarNotification($data->request, auth()->user());
        if ($data->previouslyApproved) {
            $this->requestEmailService->sendCancelledRequestCarMail($data->request);
        }
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedRequest(ApprovedCarRequest $request): Response
    {
        $dto = $request->toDTO();
        $request = $this->requestCarService->approvedRequest($dto);
        $this->notificationService->approvedRequestCarNotification($request);
        $this->requestEmailService->sendApprovedRequestCarMail($request);
        return $this->noContentResponse();
    }

    public function getBusyDaysForProposalCalendar(): JsonResponse
    {
        $data = $this->requestCarService->getBusyDaysForProposalCalendar();
        return $this->showAll($data);
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalRequest(ProposalCarRequest $request): Response
    {
        $dto = $request->toDTO();
        $request = $this->requestCarService->proposalRequest($dto);
        $this->notificationService->proposalCarRequestNotification($request);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function responseRejectRequest(int $requestId, ResponseRejectRequest $request): Response
    {
        $dto = $request->toDTO();
        $request = $this->requestCarService->responseRejectRequest($requestId, $dto);
        $this->notificationService->responseRejectCarRequestNotification($request);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadZipImages(int $id, UploadZipImagesCarRequest $request): Response
    {
        $dto = $request->toDTO();
        $this->requestCarService->uploadZipImages($id, $dto);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function addExtraCarInformation(int $id, AddExtraInformationCarRequest $request): Response
    {
        $dto = $request->toDTO();
        $this->requestCarService->addExtraCarInformation($id, $dto);
        return $this->noContentResponse();
    }
}
