<?php

namespace App\Observers;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestNotificationServiceInterface;
use App\Helpers\Utils;
use App\Models\RequestDriver;

class RequestDriverObserver
{
    private $notificationService;
    private $requestNotificationService;

    function __construct(NotificationServiceInterface $notificationService,
                        RequestNotificationServiceInterface $requestNotificationService){
        $this->notificationService = $notificationService;
        $this->requestNotificationService = $requestNotificationService;
    }

    public function created(RequestDriver $requestDriver)
    {
        $notification = $this->notificationService->createRequestDriverNotification($requestDriver->fresh('request'));
        $this->requestNotificationService->create($requestDriver->request_id, $notification->id);
        Utils::eventAlertNotification($notification);
    }
}
