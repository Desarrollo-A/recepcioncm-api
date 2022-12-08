<?php

namespace App\Services;

use App\Contracts\Repositories\ActionRequestNotificationRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Repositories\RequestNotificationRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Utils;
use App\Models\Dto\ActionRequestNotificationDTO;
use App\Models\Dto\NotificationDTO;
use App\Models\Dto\RequestNotificationDTO;
use App\Models\Enums\Lookups\ActionRequestNotificationLookup;
use App\Models\Enums\Lookups\NotificationColorLookup;
use App\Models\Enums\Lookups\NotificationIconLookup;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\Enums\Lookups\TypeNotificationsLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\Inventory;
use App\Models\Notification;
use App\Models\Package;
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
    protected $actionRequestNotificationRepository;
    protected $userRepository;

    public function __construct(NotificationRepositoryInterface              $notificationRepository,
                                LookupRepositoryInterface                    $lookupRepository,
                                RequestRepositoryInterface                   $requestRepository,
                                ActionRequestNotificationRepositoryInterface $actionRequestNotificationRepository,
                                RequestNotificationRepositoryInterface       $requestNotificationRepository,
                                UserRepositoryInterface                      $userRepository)
    {
        $this->entityRepository = $notificationRepository;
        $this->lookupRepository = $lookupRepository;
        $this->requestRepository = $requestRepository;
        $this->actionRequestNotificationRepository = $actionRequestNotificationRepository;
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
            'message' => "La solicitud de sala $request->code fue " . StatusRoomRequestLookup::APPROVED,
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
        Utils::eventAlertNotification($notification);
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
            'message' => "La solicitud de sala $request->code fue " . StatusRoomRequestLookup::CANCELLED,
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
        if ($request->status->code === StatusRoomRequestLookup::code(StatusRoomRequestLookup::REJECTED)) {
            $message = "Propuesta de la solicitud de sala $request->code fue " . StatusRoomRequestLookup::REJECTED;
            $colorId = $this->getColorId(NotificationColorLookup::RED);
        } else if ($request->status->code === StatusRoomRequestLookup::code(StatusRoomRequestLookup::IN_REVIEW)) {
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
            $this->createActionNotification($notificationDTO, $request, ActionRequestNotificationLookup::CONFIRM);
        });

        $this->actionRequestNotificationRepository->updatePastRecords();
    }

    /**
     * @throws CustomErrorException
     */
    public function createScoreRequestNotification(Collection $requests)
    {
        $requests->each(function (Request $request) {
            $notificationDTO = new NotificationDTO([
                'message' => "Calificar la solicitud $request->code",
                'user_id' => $request->user_id,
                'type_id' => $this->getTypeId(TypeNotificationsLookup::GENERAL),
                'color_id' => $this->getColorId(NotificationColorLookup::BLUE),
                'icon_id' => $this->getIconId(NotificationIconLookup::STAR)
            ]);

            $this->createActionNotification($notificationDTO, $request, ActionRequestNotificationLookup::SCORE);
        });
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
        Utils::eventAlertNotification($notification);
    }

    public function createRequestPackageNotification(Package $package): Notification
    {
        $userId = $this->userRepository->findByOfficeIdAndRoleRecepcionist($package->office_id)->id;
        $notificationDTO = new NotificationDTO([
            'message' => "Nueva solicitud de paquetería {$package->request->code}",
            'user_id' => $userId,
            'type_id' => $this->getTypeId(TypeNotificationsLookup::PARCEL),
            'color_id' => $this->getColorId(NotificationColorLookup::BLUE),
            'icon_id' => $this->getIconId(NotificationIconLookup::TRUCK)
        ]);
        return $this->createRow($notificationDTO);
    }

    public function deleteRequestPackageNotification (Package $package): void
    {
        $userId = $this->userRepository->findByOfficeIdAndRoleRecepcionist($package->office_id)->id;
        $notificationDTO = new NotificationDTO([
            'message' => "La solicitud de paquetería {$package->request->code} fue eliminada",
            'user_id' => $userId,
            'type_id' => $this->getTypeId(TypeNotificationsLookup::PARCEL),
            'color_id' => $this->getColorId(NotificationColorLookup::RED),
            'icon_id' => $this->getIconId(NotificationIconLookup::TRUCK)
        ]);
        $notificationDelete = $this->createRow($notificationDTO);
        Utils::eventAlertNotification($notificationDelete);
    }
    /**
     * @return void
     * @throws CustomErrorException
     */
    private function createActionNotification(NotificationDTO $notificationDTO, Request $request, string $lookup)
    {
        $notification = $this->createRow($notificationDTO);
        $requestNotificationDTO = new RequestNotificationDTO([
            'notification_id' => $notification->id,
            'request_id' => $request->id
        ]);
        $requestNotification = $this->requestNotificationRepository->create($requestNotificationDTO->toArray([
            'notification_id', 'request_id'
        ]));
        $actionRequestNotificationDTO = new ActionRequestNotificationDTO([
            'request_notification_id' => $requestNotification->id,
            'type_id' => $this->lookupRepository->findByCodeAndType(
                ActionRequestNotificationLookup::code($lookup),
                TypeLookup::ACTION_REQUEST_NOTIFICATION)->id
        ]);
        $this->actionRequestNotificationRepository->create($actionRequestNotificationDTO->toArray([
            'request_notification_id', 'type_id'
        ]));

        Utils::eventAlertNotification($notification);
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

}