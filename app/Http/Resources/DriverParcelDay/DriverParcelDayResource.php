<?php

namespace App\Http\Resources\DriverParcelDay;

use App\Http\Resources\Lookup\LookupResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverParcelDayResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'driverId' => $this->driver_id,
            'dayId' => $this->day_id,
            'day' => LookupResource::make($this->whenLoaded('day'))
        ];
    }
}
