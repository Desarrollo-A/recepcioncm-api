<?php

namespace App\Observers;

use App\Contracts\Services\NotificationServiceInterface;
use App\Models\RequestCar;

class RequestCarObserver
{
    private $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(RequestCar $requestCar)
    {
        $this->notificationService->createRequestCarNotification($requestCar->fresh('request'));
    }
}
