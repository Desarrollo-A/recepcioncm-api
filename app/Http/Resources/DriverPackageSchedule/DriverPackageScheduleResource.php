<?php

namespace App\Http\Resources\DriverPackageSchedule;

use App\Http\Resources\CarSchedule\CarScheduleResource;
use App\Http\Resources\DriverSchedule\DriverScheduleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverPackageScheduleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'packageId' => $this->package_id,
            'driverScheduleId' => $this->driver_schedule_id,
            'driverSchedule' => DriverScheduleResource::make($this->whenLoaded('driverSchedule')),
            'carScheduleId' => $this->car_schedule_id,
            'carSchedule' => CarScheduleResource::make($this->whenLoaded('carSchedule'))
        ];
    }
}
