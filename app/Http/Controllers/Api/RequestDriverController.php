<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestDriverServiceInterface;
use App\Contracts\Services\RequestNotificationServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Helpers\Utils;
use App\Http\Requests\CancelRequest\CancelRequest;
use App\Http\Requests\RequestDriver\ApprovedDriverRequest;
use App\Http\Requests\RequestDriver\StoreRequestDriverRequest;
use App\Http\Requests\RequestDriver\TransferDriverRequest;
use App\Http\Requests\RequestDriver\UploadFileDriverRequest;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\RequestDriver\RequestDriverResource;
use App\Http\Resources\RequestDriver\RequestDriverViewCollection;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestDriverController extends BaseApiController
{
    private $requestDriverService;
    private $notificationServiceInterface;
    private $requestNotificationServiceInterface;

    public function __construct(RequestDriverServiceInterface $requestDriverService,
                                NotificationServiceInterface $notificationServiceInterface,
                                RequestNotificationServiceInterface  $requestNotificationServiceInterface)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'uploadAuthorizationFile');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'show', 'getStatusByStatusCurrent', 'cancelRequest');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('transferRequest');

        $this->requestDriverService = $requestDriverService;
        $this->notificationServiceInterface = $notificationServiceInterface;
        $this->requestNotificationServiceInterface = $requestNotificationServiceInterface;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreRequestDriverRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $requestDriver = $this->requestDriverService->create($dto);
        return $this->showOne(new RequestDriverResource($requestDriver));
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadAuthorizationFile(int $requestId, UploadFileDriverRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $this->requestDriverService->uploadAuthorizationFile($requestId, $dto);
        return $this->noContentResponse();
    }

    public function index(Request $request): JsonResponse
    {
        $requestDrivers = $this->requestDriverService->findAllDriversPaginated($request, auth()->user());
        return $this->showAll(new RequestDriverViewCollection($requestDrivers , true));
    }

    public function show(int $requestId): JsonResponse
    {
        $requestDriver = $this->requestDriverService->findByDriverRequestId($requestId, auth()->user());
        return $this->showOne(new RequestDriverResource($requestDriver));
    }

    public function getStatusByStatusCurrent(string $code): JsonResponse
    {
        $roleName = auth()->user()->role->name;
        $status = $this->requestDriverService->getStatusByStatusCurrent($code, $roleName);
        return $this->showAll(LookupResource::collection($status));
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequest(int $requestId, CancelRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $dto->request_id = $requestId;
        $requestDriver = $this->requestDriverService->cancelRequest($dto);
        $notificationCancel = $this->notificationServiceInterface->cancelRequestDriverNotification($requestDriver->fresh('requestDriver'), auth()->user());
        $this->requestNotificationServiceInterface->create($requestDriver->id, $notificationCancel->id);
        Utils::eventAlertNotification($notificationCancel);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function transferRequest(int $requestDriverId, TransferDriverRequest $request): JsonResponse
    {
        $requestDriver = $this->requestDriverService->transferRequest($requestDriverId, $request->toDTO());
        $notificationTransfer = $this->notificationServiceInterface->transferRequestDriverNotification($requestDriver);
        $this->requestNotificationServiceInterface->create($requestDriver->request_id, $notificationTransfer->id);
        Utils::eventAlertNotification($notificationTransfer);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedRequest(ApprovedDriverRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $request = $this->requestDriverService->approvedRequest($dto);
        $notificationApproved = $this->notificationServiceInterface->approvedRequestDriverNotification($request);
        $this->requestNotificationServiceInterface->create($request->id, $notificationApproved->id);
        Utils::eventAlertNotification($notificationApproved);
        return $this->noContentResponse();
    }
}
