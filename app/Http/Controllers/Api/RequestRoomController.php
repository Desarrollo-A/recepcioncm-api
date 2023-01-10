<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\InventoryRequestServiceInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Contracts\Services\LookupServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestEmailServiceInterface;
use App\Contracts\Services\RequestRoomServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\CancelRequest\CancelRequest;
use App\Http\Requests\Request\ResponseRejectRequest;
use App\Http\Requests\RequestRoom\AssignSnackRequest;
use App\Http\Requests\RequestRoom\ProposalRequestRoomRequest;
use App\Http\Requests\RequestRoom\StoreRequestRoomRequest;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Request\AvailableScheduleResource;
use App\Http\Resources\RequestRoom\RequestRoomResource;
use App\Http\Resources\RequestRoom\RequestRoomViewCollection;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestRoomController extends BaseApiController
{
    private $requestRoomService;
    private $lookupService;
    private $inventoryService;
    private $inventoryRequestService;
    private $notificationService;
    private $requestEmailService;

    public function __construct(RequestRoomServiceInterface $requestRoomService,
                                LookupServiceInterface $lookupService,
                                InventoryServiceInterface $inventoryService,
                                InventoryRequestServiceInterface $inventoryRequestService,
                                NotificationServiceInterface $notificationService,
                                RequestEmailServiceInterface $requestEmailService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'responseRejectRequest');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'show', 'getStatusByStatusCurrent', 'cancelRequest');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('assignSnack', 'getAvailableScheduleByDay', 'withoutAttendingRequest', 'proposalRequest');

        $this->requestRoomService = $requestRoomService;
        $this->lookupService = $lookupService;
        $this->inventoryService = $inventoryService;
        $this->inventoryRequestService = $inventoryRequestService;
        $this->notificationService = $notificationService;
        $this->requestEmailService = $requestEmailService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $requestRooms = $this->requestRoomService->findAllRoomsPaginated($request, $user);
        return $this->showAll(new RequestRoomViewCollection($requestRooms, true));
    }

    public function show(int $id): JsonResponse
    {
        $requestRoom = $this->requestRoomService->findByRequestId($id, auth()->user());
        return $this->showOne(new RequestRoomResource($requestRoom));
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreRequestRoomRequest $request): JsonResponse
    {
        $requestRoomDTO = $request->toDTO();
        $this->lookupService->validateLookup($requestRoomDTO->level_id, TypeLookup::LEVEL_MEETING,
            'Tipo de junta no válido.');
        $requestRoom = $this->requestRoomService->create($requestRoomDTO);
        return $this->showOne(new RequestRoomResource($requestRoom));
    }

    /**
     * @throws CustomErrorException
     */
    public function assignSnack(AssignSnackRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $requestModel = $this->requestRoomService->assignSnack($dto, auth()->user()->office_id);
        $this->requestRoomService->checkRequestsByDay($requestModel, auth()->id());
        $this->notificationService->newOrResponseToApprovedRequestRoomNotification($requestModel);
        $this->requestEmailService->sendApprovedRequestMail($requestModel);
        return $this->noContentResponse();
    }

    public function getStatusByStatusCurrent(string $code, Request $request): JsonResponse
    {
        $roleName = auth()->user()->role->name;
        $status = $this->requestRoomService->getStatusByStatusCurrent($code, $roleName, $request->get('request_id'));
        return $this->showAll(LookupResource::collection($status));
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequest(int $requestId, CancelRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $dto->request_id = $requestId;
        $requestModel = $this->requestRoomService->cancelRequest($dto, auth()->user());
        $snacks = $this->inventoryRequestService->deleteSnacks($requestId);
        $this->inventoryService->restoreStockAfterInventoriesRequestDeleted($snacks);
        $this->requestEmailService->sendCancelledRequestMail($requestModel);
        $this->notificationService->approvedToCancelledRequestRoomNotification($requestModel, auth()->user());
        return $this->noContentResponse();
    }

    public function getAvailableScheduleByDay(int $requestId, string $date): JsonResponse
    {
        $schedule = $this->requestRoomService->getAvailableScheduleByDay($requestId, new Carbon($date));
        return $this->showAll(AvailableScheduleResource::collection($schedule));
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalRequest(int $requestId, ProposalRequestRoomRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $requestModel = $this->requestRoomService->proposalRequest($requestId, $dto);
        $this->requestRoomService->checkRequestsByDay($requestModel, auth()->id());
        $this->notificationService->newToProposalRequestRoomNotification($requestModel);
        return $this->noContentResponse();
    }

    public function withoutAttendingRequest(int $requestId): JsonResponse
    {
        $request = $this->requestRoomService->withoutAttendingRequest($requestId);
        $snacks = $this->inventoryRequestService->deleteSnacks($requestId);
        $this->inventoryService->restoreStockAfterInventoriesRequestDeleted($snacks);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function responseRejectRequest(int $id, ResponseRejectRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $requestModel = $this->requestRoomService->responseRejectRequest($id, $dto);
        $this->notificationService->proposalToRejectedOrResponseRequestRoomNotification($requestModel);
        return $this->noContentResponse();
    }
}
