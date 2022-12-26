<?php

namespace App\Observers;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestNotificationServiceInterface;
use App\Helpers\Utils;
use App\Models\RequestCar;

class RequestCarObserver
{
    private $notificationService;
    private $requestNotificationService;

    public function __construct(NotificationServiceInterface $notificationService,
                                RequestNotificationServiceInterface $requestNotificationService)
    {
        $this->notificationService = $notificationService;
        $this->requestNotificationService = $requestNotificationService;
    }

    public function created(RequestCar $requestCar)
    {
        $notification = $this->notificationService->createRequestCarNotification($requestCar->fresh('request'));
        $this->requestNotificationService->create($requestCar->request_id, $notification->id);
        Utils::eventAlertNotification($notification);
    }
}
