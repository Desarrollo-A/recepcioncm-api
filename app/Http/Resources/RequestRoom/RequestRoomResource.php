<?php

namespace App\Http\Resources\RequestRoom;

use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Request\RequestResource;
use App\Http\Resources\Room\RoomResource;
use Illuminate\Http\Resources\Json\Resource;

class RequestRoomResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'requestId' => $this->request_id,
            'roomId' => $this->room_id,
            'externalPeople' => $this->external_people,
            'levelId' => $this->level_id,
            'request' => RequestResource::make($this->whenLoaded('request')),
            'room' => RoomResource::make($this->whenLoaded('room')),
            'level' => LookupResource::make($this->whenLoaded('level'))
        ];
    }
}
