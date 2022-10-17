<?php

namespace App\Services;

use App\Contracts\Repositories\ConfirmNotificationRepositoryInterface;
use App\Contracts\Services\ConfirmNotificationServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Models\ConfirmNotification;
use App\Models\Dto\ConfirmNotificationDTO;

class ConfirmNotificationService extends BaseService implements ConfirmNotificationServiceInterface
{
    protected $entityRepository;

    public function __construct(ConfirmNotificationRepositoryInterface $confirmNotificationRepository)
    {
        $this->entityRepository = $confirmNotificationRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(int $requestNotificationId): ConfirmNotification
    {
        $dto = new ConfirmNotificationDTO(['request_notification_id' => $requestNotificationId]);
        return $this->entityRepository->create($dto->toArray(['request_notification_id']));
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    public function wasAnswered(int $notificationId)
    {
        $dto = new ConfirmNotificationDTO(['is_answered' => true]);
        $this->entityRepository->update($notificationId, $dto->toArray(['is_answered']));
    }
}