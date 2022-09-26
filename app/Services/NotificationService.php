<?php

namespace App\Services;

use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Models\Dto\NotificationDTO;
use App\Models\Enums\Lookups\StatusRequestLookup;
use App\Models\Enums\Lookups\TypeNotificationsLookup;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use App\Models\Request;
use App\Models\RequestRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService extends BaseService implements NotificationServiceInterface
{
    protected $entityRepository;
    private $lookupRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository,
                                LookupRepositoryInterface $lookupRepository)
    {
        $this->entityRepository = $notificationRepository;
        $this->lookupRepository = $lookupRepository;
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function createRequestRoomNotification(RequestRoom $requestRoom)
    {
        $notificationDTO = new NotificationDTO([
            'message' => "Nueva solicitud de sala {$requestRoom->request->code}",
            'user_id' => $requestRoom->room->recepcionist_id,
            'request_id' => $requestRoom->request_id,
            'type_id' => $this->lookupRepository->findByCodeAndType(TypeNotificationsLookup::code(TypeNotificationsLookup::ROOM),
                TypeLookup::REQUEST_TYPE_NOTIFICATIONS)->id
        ]);
        $this->entityRepository->create($notificationDTO->toArray(['message', 'user_id', 'request_id', 'type_id']));
    }

    public function getAllNotificationUnread(int $userId): Collection
    {
        return $this->entityRepository->getAllNotificationUnread($userId);
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function newOrResponseToApprovedRequestRoomNotification(Request $request)
    {
        $notificationDTO = new NotificationDTO([
            'message' => "La solicitud de sala $request->code fue ".StatusRequestLookup::APPROVED,
            'user_id' => $request->user_id,
            'request_id' => $request->id,
            'type_id' => $this->lookupRepository->findByCodeAndType(TypeNotificationsLookup::code(TypeNotificationsLookup::ROOM),
                TypeLookup::REQUEST_TYPE_NOTIFICATIONS)->id
        ]);
        $this->entityRepository->create($notificationDTO->toArray(['message', 'user_id', 'request_id', 'type_id']));
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function newToProposalRequestRoomNotification(Request $request)
    {
        $notificationDTO = new NotificationDTO([
            'message' => "Propuesta de la solicitud de sala $request->code",
            'user_id' => $request->user_id,
            'request_id' => $request->id,
            'type_id' => $this->lookupRepository->findByCodeAndType(TypeNotificationsLookup::code(TypeNotificationsLookup::ROOM),
                TypeLookup::REQUEST_TYPE_NOTIFICATIONS)->id
        ]);
        $this->entityRepository->create($notificationDTO->toArray(['message', 'user_id', 'request_id', 'type_id']));
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
            'type_id' => $this->lookupRepository->findByCodeAndType(TypeNotificationsLookup::code(TypeNotificationsLookup::GENERAL),
                TypeLookup::REQUEST_TYPE_NOTIFICATIONS)->id
        ]);
        $this->entityRepository->create($notificationDTO->toArray(['message', 'user_id', 'request_id', 'type_id']));
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function approvedToCancelledRequestRoomNotification(Request $request, User $user)
    {
        $userId = ($user->role->name === NameRole::RECEPCIONIST)
            ? $request->user_id
            : $request->requestRoom->room->recepcionist_id;

        $notificationDTO = new NotificationDTO([
            'message' => "La solicitud de sala $request->code fue ".StatusRequestLookup::CANCELLED,
            'user_id' => $userId,
            'request_id' => $request->id,
            'type_id' => $this->lookupRepository->findByCodeAndType(TypeNotificationsLookup::code(TypeNotificationsLookup::ROOM),
                TypeLookup::REQUEST_TYPE_NOTIFICATIONS)->id
        ]);
        $this->entityRepository->create($notificationDTO->toArray(['message', 'user_id', 'request_id', 'type_id']));
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function proposalToRejectedOrResponseRequestRoomNotification(Request $request)
    {
        $status = $this->lookupRepository->findById($request->status_id);
        $message = '';
        if ($status->code === StatusRequestLookup::code(StatusRequestLookup::REJECTED)) {
            $message = "Propuesta de la solicitud de sala $request->code fue ".StatusRequestLookup::REJECTED;
        } else if ($status->code === StatusRequestLookup::code(StatusRequestLookup::IN_REVIEW)) {
            $message = "Propuesta de la solicitud de sala $request->code fue Aceptada";
        }

        $notificationDTO = new NotificationDTO([
            'message' => $message,
            'user_id' => $request->requestRoom->room->recepcionist_id,
            'request_id' => $request->id,
            'type_id' => $this->lookupRepository->findByCodeAndType(TypeNotificationsLookup::code(TypeNotificationsLookup::ROOM),
                TypeLookup::REQUEST_TYPE_NOTIFICATIONS)->id
        ]);
        $this->entityRepository->create($notificationDTO->toArray(['message', 'user_id', 'request_id', 'type_id']));
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
}