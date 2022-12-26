<?php

namespace App\Http\Resources\CarRequestSchedule;

use App\Http\Resources\CarSchedule\CarScheduleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CarRequestScheduleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'requestCarId' => $this->request_car_id,
            'carScheduleId' => $this->car_schedule_id,
            'carSchedule' => CarScheduleResource::make($this->whenLoaded('carSchedule'))
        ];
    }
}
