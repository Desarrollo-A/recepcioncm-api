<?php

namespace App\Http\Resources\CarSchedule;

use App\Http\Resources\Car\CarResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CarScheduleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'carId' => $this->car_id,
            'car' => CarResource::make($this->whenLoaded('car')),
            'startDate' => $this->start_date->toDateTimeString(),
            'endDate' => $this->end_date->toDateTimeString(),
        ];
    }
}
