<?php

namespace App\Services;

use App\Contracts\Repositories\RequestNotificationRepositoryInterface;
use App\Contracts\Services\RequestNotificationServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Models\Dto\RequestNotificationDTO;
use App\Models\RequestNotification;

class RequestNotificationService extends BaseService implements RequestNotificationServiceInterface
{
    protected $entityRepository;

    public function __construct(RequestNotificationRepositoryInterface $requestNotificationRepository)
    {
        $this->entityRepository = $requestNotificationRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(int $requestId, int $notificationId): RequestNotification
    {
        $dto = new RequestNotificationDTO([
            'request_id' => $requestId,
            'notification_id' => $notificationId
        ]);
        return $this->entityRepository->create($dto->toArray(['notification_id', 'request_id']));
    }
}