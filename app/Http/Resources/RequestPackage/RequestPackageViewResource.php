<?php

namespace App\Http\Resources\RequestPackage;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestPackageViewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'startDate' => $this->start_date->toDateTimeString(),
            'statusName' => $this->status_name,
            'fullName' => $this->full_name,
            'statePickup' => $this->state_pickup,
            'stateArrival' => $this->state_arrival
        ];
    }
}
