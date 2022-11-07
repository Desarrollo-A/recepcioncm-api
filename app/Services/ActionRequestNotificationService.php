<?php

namespace App\Services;

use App\Contracts\Repositories\ActionRequestNotificationRepositoryInterface;
use App\Contracts\Services\ActionRequestNotificationServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Models\ActionRequestNotification;
use App\Models\Dto\ActionRequestNotificationDTO;

class ActionRequestNotificationService extends BaseService implements ActionRequestNotificationServiceInterface
{
    protected $entityRepository;

    public function __construct(ActionRequestNotificationRepositoryInterface $actionRequestNotificationRepository)
    {
        $this->entityRepository = $actionRequestNotificationRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(int $requestNotificationId, int $typeId): ActionRequestNotification
    {
        $dto = new ActionRequestNotificationDTO([
            'request_notification_id' => $requestNotificationId,
            'type_id' => $typeId
        ]);
        return $this->entityRepository->create($dto->toArray(['request_notification_id', 'type_id']));
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    public function wasAnswered(int $notificationId)
    {
        $dto = new ActionRequestNotificationDTO(['is_answered' => true]);
        $this->entityRepository->update($notificationId, $dto->toArray(['is_answered']));
    }
}