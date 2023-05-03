<?php

namespace App\Observers;

use App\Contracts\Services\MovementRequestServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Models\RequestDriver;

class RequestDriverObserver
{
    private $notificationService;
    private $movementRequestService;

    function __construct(
        NotificationServiceInterface $notificationService,
        MovementRequestServiceInterface $movementRequestService
    )
    {
        $this->notificationService = $notificationService;
        $this->movementRequestService = $movementRequestService;
    }

    public function created(RequestDriver $requestDriver)
    {
        $data = $requestDriver->fresh('request');
        $this->notificationService->createRequestDriverNotification($data);
        $this->movementRequestService->create($data->request_id, auth()->id(), 'Solicitud creada');
    }
}
