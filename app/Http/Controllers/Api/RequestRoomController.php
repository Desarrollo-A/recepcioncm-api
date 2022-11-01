<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\InventoryRequestServiceInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Contracts\Services\LookupServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestEmailServiceInterface;
use App\Contracts\Services\RequestNotificationServiceInterface;
use App\Contracts\Services\RequestRoomServiceInterface;
use App\Core\BaseApiController;
use App\Events\AlertNotification;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\RequestRoom\AssignSnackRequest;
use App\Http\Requests\RequestRoom\AvailableScheduleRequestRoomRequest;
use App\Http\Requests\RequestRoom\CancelRequestRoomRequest;
use App\Http\Requests\RequestRoom\ProposalRequestRoomRequest;
use App\Http\Requests\RequestRoom\StoreRequestRoomRequest;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Notification\NotificationResource;
use App\Http\Resources\Request\AvailableScheduleResource;
use App\Http\Resources\RequestRoom\RequestRoomResource;
use App\Http\Resources\RequestRoom\RequestRoomViewCollection;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestRoomController extends BaseApiController
{
    private $requestRoomService;
    private $lookupService;
    private $inventoryService;
    private $inventoryRequestService;
    private $notificationService;
    private $requestNotificationService;
    private $requestEmailService;

    public function __construct(RequestRoomServiceInterface $requestRoomService,
                                LookupServiceInterface $lookupService,
                                InventoryServiceInterface $inventoryService,
                                InventoryRequestServiceInterface $inventoryRequestService,
                                NotificationServiceInterface $notificationService,
                                RequestNotificationServiceInterface $requestNotificationService,
                                RequestEmailServiceInterface $requestEmailService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)->only('store');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'show', 'getStatusByStatusCurrent', 'cancelRequest');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('isAvailableSchedule', 'assignSnack', 'getAvailableScheduleByDay', 'withoutAttendingRequest');

        $this->requestRoomService = $requestRoomService;
        $this->lookupService = $lookupService;
        $this->inventoryService = $inventoryService;
        $this->inventoryRequestService = $inventoryRequestService;
        $this->notificationService = $notificationService;
        $this->requestNotificationService = $requestNotificationService;
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
    public function isAvailableSchedule(AvailableScheduleRequestRoomRequest $request): JsonResponse
    {
        $requestDTO = $request->toDTO();
        $isAvailable = $this->requestRoomService->isAvailableSchedule($requestDTO->start_date, $requestDTO->end_date);
        return $this->successResponse(['isAvailable' => $isAvailable], Response::HTTP_OK);
    }

    /**
     * @throws CustomErrorException
     */
    public function assignSnack(AssignSnackRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $officeId = auth()->user()->office_id;
        $requestModel = $this->requestRoomService->assignSnack($dto, $officeId);
        $notification = $this->notificationService->newOrResponseToApprovedRequestRoomNotification($requestModel);
        $this->requestEmailService->sendMailNotification($requestModel);
        $this->requestNotificationService->create($requestModel->id, $notification->id);
        $this->eventNotification($notification);
        return $this->noContentResponse();
    }

    public function getStatusByStatusCurrent(string $code): JsonResponse
    {
        $roleName = auth()->user()->role->name;
        $status = $this->requestRoomService->getStatusByStatusCurrent($code, $roleName);
        return $this->showAll(LookupResource::collection($status));
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequest(int $requestId, CancelRequestRoomRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $dto->request_id = $requestId;
        $requestModel = $this->requestRoomService->cancelRequest($dto, auth()->user());
        $snacks = $this->inventoryRequestService->deleteSnacks($requestId);
        $this->inventoryService->restoreStockAfterInventoriesRequestDeleted($snacks);
        $notification = $this->notificationService->approvedToCancelledRequestRoomNotification($requestModel, auth()->user());
        $this->requestNotificationService->create($requestModel->id, $notification->id);
        $this->eventNotification($notification);
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
        $notification = $this->notificationService->newToProposalRequestRoomNotification($requestModel);
        $this->requestNotificationService->create($requestModel->id, $notification->id);
        $this->eventNotification($notification);
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
     * @return void
     */
    private function eventNotification(Notification $notification)
    {
        $newNotification = $notification->fresh(['type', 'color', 'icon', 'requestNotification',
            'requestNotification.request', 'requestNotification.confirmNotification']);
        broadcast(new AlertNotification($notification->user_id, new NotificationResource($newNotification)));
    }
}
