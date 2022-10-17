<?php

namespace App\Http\Resources\RequestNotification;

use App\Http\Resources\ConfirmNotification\ConfirmNotificationResource;
use App\Http\Resources\Request\RequestResource;
use Illuminate\Http\Resources\Json\Resource;

class RequestNotificationResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'notificationId' => $this->notification_id,
            'requestId' => $this->request_id,
            'request' => RequestResource::make($this->whenLoaded('request')),
            'confirmNotification' => ConfirmNotificationResource::make($this->whenLoaded('confirmNotification'))
        ];
    }
}
