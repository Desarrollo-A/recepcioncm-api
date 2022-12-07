<?php

namespace App\Observers;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestNotificationServiceInterface;
use App\Helpers\Utils;
use App\Models\Package;

class PackageObserver
{
    private $notificationService;
    private $requestNotificationService;

    function __construct(NotificationServiceInterface $notificationService,
                        RequestNotificationServiceInterface $requestNotificationService){
        $this->notificationService = $notificationService;
        $this->requestNotificationService = $requestNotificationService;
    }

    public function created(Package $package)
    {
        $notification = $this->notificationService->createRequestPackageNotification($package->fresh(['request']));
        $this->requestNotificationService->create($package->request_id, $notification->id);
        Utils::eventAlertNotification($notification);
    }
}   