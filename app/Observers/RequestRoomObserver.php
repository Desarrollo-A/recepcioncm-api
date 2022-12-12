<?php

namespace App\Observers;

use App\Contracts\Services\NotificationServiceInterface;
use App\Contracts\Services\RequestNotificationServiceInterface;
use App\Events\AlertNotification;
use App\Helpers\Utils;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\RequestRoom;

class RequestRoomObserver
{
    private $notificationService;
    private $requestNotificationService;

    public function __construct(NotificationServiceInterface $notificationService,
                                RequestNotificationServiceInterface $requestNotificationService)
    {
        $this->notificationService = $notificationService;
        $this->requestNotificationService = $requestNotificationService;
    }

    /**
     * Handle the RequestRoom "created" event.
     *
     * @param \App\Models\RequestRoom $requestRoom
     * @return void
     */
    public function created(RequestRoom $requestRoom)
    {
        $notification = $this->notificationService->createRequestRoomNotification($requestRoom->fresh(['request', 'room']));
        $this->requestNotificationService->create($requestRoom->request_id, $notification->id);
        Utils::eventAlertNotification($notification);
    }
}