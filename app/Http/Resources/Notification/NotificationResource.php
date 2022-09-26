<?php

namespace App\Http\Resources\Notification;

use App\Http\Resources\Lookup\LookupResource;
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
            'requestId' => $this->request_id,
            'typeId' => $this->type_id,
            'createdAt' => $this->created_at,
            'type' => LookupResource::make($this->whenLoaded('type'))
        ];
    }
}
