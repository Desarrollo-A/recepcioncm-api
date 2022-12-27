<?php

namespace App\Observers;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestNotificationServiceInterface;
use App\Helpers\Utils;
use App\Models\Package;

class PackageObserver
{
    private $notificationService;

    function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(Package $package)
    {
        $this->notificationService->createRequestPackageNotification($package->fresh(['request']));
    }
}   