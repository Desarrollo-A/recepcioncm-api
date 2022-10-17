<?php

namespace App\Http\Resources\ConfirmNotification;

use Illuminate\Http\Resources\Json\Resource;

class ConfirmNotificationResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'requestNotificationId' => $this->request_notification_id,
            'isAnswered' => $this->is_answered
        ];
    }
}
