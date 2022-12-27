<?php

namespace App\Observers;

use App\Contracts\Services\NotificationServiceInterface;
use App\Models\RequestDriver;

class RequestDriverObserver
{
    private $notificationService;

    function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(RequestDriver $requestDriver)
    {
        $this->notificationService->createRequestDriverNotification($requestDriver->fresh('request'));
    }
}
