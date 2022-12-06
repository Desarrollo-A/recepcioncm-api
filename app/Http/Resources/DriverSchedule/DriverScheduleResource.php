<?php

namespace App\Http\Resources\DriverSchedule;

use App\Http\Resources\Driver\DriverResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverScheduleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'driverId' => $this->driver_id,
            'driver' => DriverResource::make($this->whenLoaded('driver')),
            'startDate' => $this->start_date->toDateTimeString(),
            'endDate' => $this->end_date->toDateTimeString(),
        ];
    }
}
