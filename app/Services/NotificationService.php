<?php

namespace App\Services;

use App\Contracts\Repositories\ConfirmNotificationRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Repositories\RequestNotificationRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Core\BaseService;
use App\Events\AlertNotification;
use App\Exceptions\CustomErrorException;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\Dto\ConfirmNotificationDTO;
use App\Models\Dto\NotificationDTO;
use App\Models\Dto\RequestNotificationDTO;
use App\Models\Enums\Lookups\NotificationColorLookup;
use App\Models\Enums\Lookups\NotificationIconLookup;
use App\Models\Enums\Lookups\StatusRequestLookup;
use App\Models\Enums\Lookups\TypeNotificationsLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\Inventory;
use App\Models\Notification;
use App\Models\Request;
use App\Models\RequestRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService extends BaseService implements NotificationServiceInterface
{
    protected $entityRepository;
    protected $lookupRepository;
    protected $requestRepository;
    protected $requestNotificationRepository;
    protected $confirmNotificationRepository;
    protected $userRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository,
                                LookupRepositoryInterface $lookupRepository,
                                RequestRepositoryInterface $requestRepository,
                                ConfirmNotificationRepositoryInterface $confirmNotificationRepository,
                                RequestNotificationRepositoryInterface $requestNotificationRepository,
                                UserRepositoryInterface $userRepository)
    {
        $this->entityRepository = $notificationRepository;
        $this->lookupRepository = $lookupRepository;
        $this->requestRepository = $requestRepository;
        $this->confirmNotificationRepository = $confirmNotificationRepository;
        $this->requestNotificationRepository = $requestNotificationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function createRequestRoomNotification(RequestRoom $requestRoom): Notification
    {
        $notificationDTO = new NotificationDTO([
            'message' => "Nueva solicitud de sala {$requestRoom->request->code}",
            'user_id' => $requestRoom->room->recepcionist_id,
            'type_id' => $this->getTypeId(TypeNotificationsLookup::ROOM),
            'color_id' => $this->getColorId(NotificationColorLookup::BLUE),
            'icon_id' => $this->getIconId(NotificationIconLookup::ROOM)
        ]);
        return $this->createRow($notificationDTO);
    }

    public function getAllNotificationLast5Days(int $userId): Collection
    {
        return $this->entityRepository->getAllNotificationLast5Days($userId);
    }

    /**
     * @throws CustomErrorException
     */
    public function newOrResponseToApprovedRequestRoomNotification(Request $request): Notification
    {
        $notificationDTO = new NotificationDTO([
            'message' => "La solicitud de sala $request->code fue " . StatusRequestLookup::APPROVED,
            'user_id' => $request->user_id,
            'type_id' => $this->getTypeId(TypeNotificationsLookup::ROOM),
            'color_id' => $this->getColorId(NotificationColorLookup::GREEN),
            'icon_id' => $this->getIconId(NotificationIconLookup::ROOM)
        ]);
        return $this->createRow($notificationDTO);
    }

    /**
     * @throws CustomErrorException
     */
    public function newToProposalRequestRoomNotification(Request $request): Notification
    {
        $notificationDTO = new NotificationDTO([
            'message' => "Propuesta de la solicitud de sala $request->code",
            'user_id' => $request->user_id,
            'type_id' => $this->getTypeId(TypeNotificationsLookup::ROOM),
            'color_id' => $this->getColorId(NotificationColorLookup::ORANGE),
            'icon_id' => $this->getIconId(NotificationIconLookup::ROOM)
        ]);
        return $this->createRow($notificationDTO);
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function newToDeletedRequestRoomNotification(Request $request)
    {
        $notificationDTO = new NotificationDTO([
            'message' => "La solicitud $request->code fue Eliminada",
            'user_id' => $request->requestRoom->room->recepcionist_id,
            'type_id' => $this->getTypeId(TypeNotificationsLookup::ROOM),
            'color_id' => $this->getColorId(NotificationColorLookup::RED),
            'icon_id' => $this->getIconId(NotificationIconLookup::ROOM)
        ]);
        $notification = $this->createRow($notificationDTO);
        $this->eventNotification($notification);
    }

    /**
     * @throws CustomErrorException
     */
    public function approvedToCancelledRequestRoomNotification(Request $request, User $user): Notification
    {
        $userId = ($user->role->name === NameRole::RECEPCIONIST)
            ? $request->user_id
            : $request->requestRoom->room->recepcionist_id;

        $notificationDTO = new NotificationDTO([
            'message' => "La solicitud de sala $request->code fue " . StatusRequestLookup::CANCELLED,
            'user_id' => $userId,
            'type_id' => $this->getTypeId(TypeNotificationsLookup::ROOM),
            'color_id' => $this->getColorId(NotificationColorLookup::RED),
            'icon_id' => $this->getIconId(NotificationIconLookup::ROOM)
        ]);
        return $this->createRow($notificationDTO);
    }

    /**
     * @throws CustomErrorException
     */
    public function proposalToRejectedOrResponseRequestRoomNotification(Request $request): Notification
    {
        $message = '';
        $colorId = null;
        if ($request->status->code === StatusRequestLookup::code(StatusRequestLookup::REJECTED)) {
            $message = "Propuesta de la solicitud de sala $request->code fue " . StatusRequestLookup::REJECTED;
            $colorId = $this->getColorId(NotificationColorLookup::RED);
        } else if ($request->status->code === StatusRequestLookup::code(StatusRequestLookup::IN_REVIEW)) {
            $message = "Propuesta de la solicitud de sala $request->code fue Aceptada";
            $colorId = $this->getColorId(NotificationColorLookup::GREEN);
        }

        $notificationDTO = new NotificationDTO([
            'message' => $message,
            'user_id' => $request->requestRoom->room->recepcionist_id,
            'type_id' => $this->getTypeId(TypeNotificationsLookup::ROOM),
            'color_id' => $colorId,
            'icon_id' => $this->getIconId(NotificationIconLookup::ROOM)
        ]);
        return $this->createRow($notificationDTO);
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function readNotification(int $id)
    {
        $dto = new NotificationDTO(['is_read' => true]);
        $this->entityRepository->update($id, $dto->toArray(['is_read']));
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function readAllNotificationUser(int $userId)
    {
        $dto = new NotificationDTO(['is_read' => true]);
        $this->entityRepository->massiveNotificationUserUpdate($userId, $dto->toArray(['is_read']));
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function createConfirmNotification()
    {
        $this->requestRepository->getApprovedRequestsTomorrow()->each(function (Request $request) {
            $notificationDTO = new NotificationDTO([
                'message' => "Confirmación de solicitud $request->code",
                'user_id' => $request->user_id,
                'type_id' => $this->getTypeId(TypeNotificationsLookup::ROOM),
                'color_id' => $this->getColorId(NotificationColorLookup::BLUE),
                'icon_id' => $this->getIconId(NotificationIconLookup::CONFIRM)
            ]);
            $notification = $this->createRow($notificationDTO);
            $requestNotificationDTO = new RequestNotificationDTO([
                'notification_id' => $notification->id,
                'request_id' => $request->id
            ]);
            $requestNotification = $this->requestNotificationRepository->create($requestNotificationDTO->toArray([
                'notification_id', 'request_id'
            ]));
            $confirmNotificationDTO = new ConfirmNotificationDTO(['request_notification_id' => $requestNotification->id]);
            $this->confirmNotificationRepository->create($confirmNotificationDTO->toArray(['request_notification_id']));

            $this->eventNotification($notification);
        });

        $this->confirmNotificationRepository->updatePastRecords();
    }

    /**
     * @throws CustomErrorException
     */
    public function minimumStockNotification(Inventory $inventory)
    {
        $userId = $this->userRepository->findByOfficeIdAndRoleRecepcionist($inventory->office_id)->id;
        $notificationDTO = new NotificationDTO([
            'message' => "El inventario $inventory->code se encuentra al mínimo",
            'user_id' => $userId,
            'type_id' => $this->getTypeId(TypeNotificationsLookup::INVENTORY),
            'color_id' => $this->getColorId(NotificationColorLookup::YELLOW),
            'icon_id' => $this->getIconId(NotificationIconLookup::WARNING)
        ]);
        $notification = $this->createRow($notificationDTO);
        $this->eventNotification($notification);
    }

    /**
     * @throws CustomErrorException
     */
    private function createRow(NotificationDTO $dto): Notification
    {
        return $this->entityRepository->create($dto->toArray(['message', 'user_id', 'type_id', 'color_id', 'icon_id']));
    }

    private function getColorId(string $value): int
    {
        return $this->lookupRepository->findByCodeAndType(NotificationColorLookup::code($value),
            TypeLookup::NOTIFICATION_COLOR)->id;
    }

    private function getTypeId(string $value): int
    {
        return $this->lookupRepository->findByCodeAndType(TypeNotificationsLookup::code($value),
            TypeLookup::REQUEST_TYPE_NOTIFICATIONS)->id;
    }

    private function getIconId(string $value): int
    {
        return $this->lookupRepository->findByCodeAndType(NotificationIconLookup::code($value),
            TypeLookup::NOTIFICATION_ICON)->id;
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