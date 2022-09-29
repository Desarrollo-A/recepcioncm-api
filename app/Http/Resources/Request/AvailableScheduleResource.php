<?php

namespace App\Http\Resources\Request;

use Illuminate\Http\Resources\Json\Resource;

class AvailableScheduleResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'startTime' => $this['start_time']->format('H:i'),
            'endTime' => $this['end_time']->format('H:i'),
            'startDate' => $this['start_time']->toDateTimeString(),
            'endDate' => $this['end_time']->toDateTimeString()
        ];
    }
}
