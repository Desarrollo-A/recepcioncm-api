<?php

namespace App\Observers;

use App\Contracts\Services\MovementRequestServiceInterface;
use App\Contracts\Services\NotificationServiceInterface;
use App\Models\RequestRoom;

class RequestRoomObserver
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

    /**
     * Handle the RequestRoom "created" event.
     *
     * @param RequestRoom $requestRoom
     * @return void
     */
    public function created(RequestRoom $requestRoom)
    {
        $data = $requestRoom->fresh(['request', 'room']);
        $this->notificationService->createRequestRoomNotification($data);
        $this->movementRequestService->create($data->request_id, $data->request->user_id, 'Creación de solicitud');
    }
}