<?php

namespace App\Http\Resources\RequestDriver;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestDriverViewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'requestId' => $this->request_id,
            'requestDriverId' => $this->request_driver_id,
            'code' => $this->code,
            'title' => $this->title,
            'startDate' => $this->start_date->toDateTimeString(),
            'endDate' => $this->end_date->toDateTimeString(),
            'statusName' => $this->status_name,
            'statusCode' => $this->status_code,
            'fullName' => $this->full_name,
            'statePickup' => $this->state_pickup,
            'stateArrival' => $this->state_arrival
        ];
    }
}
