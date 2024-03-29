<?php

namespace App\Http\Resources\RequestDriver;

use App\Helpers\Enum\Path;
use App\Helpers\File;
use App\Http\Resources\Address\AddressResource;
use App\Http\Resources\DriverRequestSchedule\DriverRequestScheduleResource;
use App\Http\Resources\Request\RequestResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestDriverResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'pickupAddressId' => $this->pickup_address_id,
            'arrivalAddressId' => $this->arrival_address_id,
            'requestId' => $this->request_id,
            'officeId' => $this->office_id,
            'pickupAddress' => AddressResource::make($this->whenLoaded('pickupAddress')),
            'arrivalAddress' => AddressResource::make($this->whenLoaded('arrivalAddress')),
            'request' => RequestResource::make($this->whenLoaded('request')),
            'driverRequestSchedule' => DriverRequestScheduleResource::make($this->whenLoaded('driverRequestSchedule')),
        ];
    }
}
