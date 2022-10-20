<?php

namespace App\Http\Resources\Notification;

use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\RequestNotification\RequestNotificationResource;
use Illuminate\Http\Resources\Json\Resource;

class NotificationResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'isRead' => $this->is_read,
            'userId' => $this->user_id,
            'typeId' => $this->type_id,
            'colorId' => $this->color_id,
            'iconId' => $this->icon_id,
            'createdAt' => $this->created_at->toDateTimeLocalString(),
            'type' => LookupResource::make($this->whenLoaded('type')),
            'color' => LookupResource::make($this->whenLoaded('color')),
            'icon' => LookupResource::make($this->whenLoaded('icon')),
            'requestNotification' => RequestNotificationResource::make($this->whenLoaded('requestNotification'))
        ];
    }
}
