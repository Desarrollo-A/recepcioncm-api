<?php

namespace App\Http\Resources\RequestCar;

use App\Http\Resources\CarRequestSchedule\CarRequestScheduleResource;
use App\Http\Resources\File\FileResource;
use App\Http\Resources\Office\OfficeResource;
use App\Http\Resources\Request\RequestResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestCarResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'requestId' => $this->request_id,
            'officeId' => $this->office_id,
            'initialKm' => $this->initial_km,
            'finalKm' => $this->final_km,
            'deliveryCondition' => $this->delivery_condition,
            'request' => RequestResource::make($this->whenLoaded('request')),
            'carRequestSchedule' => CarRequestScheduleResource::make($this->whenLoaded('carRequestSchedule')),
            'office' => OfficeResource::make($this->whenLoaded('office')),
            'files' => FileResource::collection($this->whenLoaded('files'))
        ];
    }
}
