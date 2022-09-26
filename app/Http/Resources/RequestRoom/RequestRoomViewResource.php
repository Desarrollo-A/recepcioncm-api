<?php

namespace App\Http\Resources\RequestRoom;

use Illuminate\Http\Resources\Json\Resource;

class RequestRoomViewResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'startDate' => $this->start_date->toDateTimeString(),
            'endDate' => $this->end_date->toDateTimeString(),
            'fullName' => $this->full_name,
            'officeId' => $this->office_id,
            'userId' => $this->user_id,
            'statusName' => $this->status_name,
            'statusCode' => $this->status_code,
            'roomName' => $this->room_name,
            'levelMeeting' => $this->level_meeting
        ];
    }
}
