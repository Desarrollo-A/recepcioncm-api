<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestCarServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\CancelRequest\CancelRequest;
use App\Http\Requests\RequestCar\ApprovedCarRequest;
use App\Http\Requests\RequestCar\StoreRequestCarRequest;
use App\Http\Requests\RequestCar\TransferCarRequest;
use App\Http\Requests\RequestCar\UploadFileRequestCarRequest;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\RequestCar\RequestCarResource;
use App\Http\Resources\RequestCar\RequestCarViewCollection;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestCarController extends BaseApiController
{
    private $requestCarService;
    private $notificationService;

    public function __construct(RequestCarServiceInterface $requestCarService,
                                NotificationServiceInterface $notificationService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'uploadAuthorizationFile', 'deleteRequestCar');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'store', 'uploadAuthorizationFile', 'show', 'cancelRequest', 'getStatusByStatusCurrent');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('transferRequest', 'approvedRequest');
        $this->requestCarService = $requestCarService;
        $this->notificationService = $notificationService;
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

    /**
     * @throws CustomErrorException
     */
    public function uploadAuthorizationFile(int $requestId, UploadFileRequestCarRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $this->requestCarService->uploadAuthorizationFile($requestId, $dto);
        return $this->noContentResponse();
    }

    public function deleteRequestCar(int $requestId): JsonResponse
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

    public function transferRequest(int $requestCarId, TransferCarRequest $request): JsonResponse
    {
        $requestCar = $this->requestCarService->transferRequest($requestCarId, $request->toDTO());
        $this->notificationService->transferRequestCarNotification($requestCar);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequest(int $requestId, CancelRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $dto->request_id = $requestId;
        $requestCar = $this->requestCarService->cancelRequest($dto);
        $this->notificationService->cancelRequestCarNotification($requestCar->fresh('requestCar'), auth()->user());
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedRequest(ApprovedCarRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $requestCar= $this->requestCarService->approvedRequest($dto);
        $this->notificationService->approvedRequestCarNotification($requestCar);
        return $this->noContentResponse();
    }
}
