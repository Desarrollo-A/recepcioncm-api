<?php

namespace App\Http\Resources\RequestRoom;

use Illuminate\Http\Resources\Json\JsonResource;

class WeekdayRoomByUser extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'total' => $this->total,
            'weekday' => $this->weekday,
            'userId' => $this->user_id
        ];
    }
}
