<?php

namespace App\Http\Resources\ActionRequestNotification;

use App\Http\Resources\Lookup\LookupResource;
use Illuminate\Http\Resources\Json\Resource;

class ActionRequestNotificationResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'requestNotificationId' => $this->request_notification_id,
            'isAnswered' => $this->is_answered,
            'typeId' => $this->type_id,
            'type' => new LookupResource($this->whenLoaded('type'))
        ];
    }
}
