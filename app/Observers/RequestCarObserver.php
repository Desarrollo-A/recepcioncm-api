<?php

namespace App\Observers;

use App\Contracts\Services\MovementRequestServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Models\RequestCar;

class RequestCarObserver
{
    private $notificationService;
    private $movementRequestService;

    public function __construct(
        NotificationServiceInterface $notificationService,
        MovementRequestServiceInterface $movementRequestService
    )
    {
        $this->notificationService = $notificationService;
        $this->movementRequestService = $movementRequestService;
    }

    public function created(RequestCar $requestCar)
    {
        $data = $requestCar->fresh('request');
        $this->notificationService->createRequestCarNotification($data);
        $this->movementRequestService->create($data->id, auth()->id(), 'Creación de solicitud');
    }
}
