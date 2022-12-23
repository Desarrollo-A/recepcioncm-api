<?php

namespace App\Http\Resources\DriverRequestSchedule;

use App\Http\Resources\CarSchedule\CarScheduleResource;
use App\Http\Resources\DriverSchedule\DriverScheduleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverRequestScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'requestDriverId'   =>  $this->request_driver_id,
            'driverScheduleId'  =>  $this->driver_schedule_id,
            'driverSchedule'    =>  DriverScheduleResource::make($this->whenLoaded('driverSchedule')),
            'carScheduleId'     =>  $this->car_schedule_id,
            'carSchedule'       =>  CarScheduleResource::make($this->whenLoaded('carSchedule'))
        ];
    }
}
