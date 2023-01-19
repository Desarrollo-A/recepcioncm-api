<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestDriverServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\CancelRequest\CancelRequest;
use App\Http\Requests\RequestDriver\ApprovedDriverRequest;
use App\Http\Requests\RequestDriver\ProposalDriverRequest;
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
    private $notificationService;
    
    public function __construct(RequestDriverServiceInterface $requestDriverService,
                                NotificationServiceInterface $notificationService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'uploadAuthorizationFile');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'show', 'getStatusByStatusCurrent', 'cancelRequest');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('transferRequest', 'approvedRequest', 'getBusyDaysForProposalCalendar');

        $this->requestDriverService = $requestDriverService;
        $this->notificationService = $notificationService;
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
        $this->notificationService->cancelRequestDriverNotification($requestDriver->fresh('requestDriver'), auth()->user());
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function transferRequest(int $requestDriverId, TransferDriverRequest $request): JsonResponse
    {
        $requestDriver = $this->requestDriverService->transferRequest($requestDriverId, $request->toDTO());
        $this->notificationService->transferRequestDriverNotification($requestDriver);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedRequest(ApprovedDriverRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $request = $this->requestDriverService->approvedRequest($dto);
        $this->notificationService->approvedRequestDriverNotification($request);
        return $this->noContentResponse();
    }

    public function getBusyDaysForProposalCalendar(): JsonResponse
    {
        $data = $this->requestDriverService->getBusyDaysForProposalCalendar();
        return $this->showAll($data);
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalRequest(ProposalDriverRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $this->requestDriverService->proposalRequest($dto);
        return $this->noContentResponse();
    }
}
