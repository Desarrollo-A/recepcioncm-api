<?php

namespace App\Events;

use App\Http\Resources\Notification\NotificationResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlertNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $userId;

    public $notification;

    public function __construct(int $userId, NotificationResource $notification)
    {
        $this->userId = $userId;
        $this->notification = $notification;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("alert.notification.$this->userId");
    }
}
