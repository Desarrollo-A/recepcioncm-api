<?php

namespace App\Observers;

use App\Contracts\Services\NotificationServiceInterface;
use App\Models\RequestRoom;

class RequestRoomObserver
{
    private $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the RequestRoom "created" event.
     *
     * @param \App\Models\RequestRoom $requestRoom
     * @return void
     */
    public function created(RequestRoom $requestRoom)
    {
        $this->notificationService->createRequestRoomNotification($requestRoom->fresh(['request', 'room']));
    }
}