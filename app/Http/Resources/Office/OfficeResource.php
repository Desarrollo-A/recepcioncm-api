<?php

namespace App\Http\Resources\Office;

use App\Http\Resources\Address\AddressResource;
use App\Http\Resources\State\StateResource;
use Illuminate\Http\Resources\Json\Resource;

class OfficeResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'addressId' => $this->address_id,
            'status' => $this->status,
            'stateId' => $this->state_id,
            'address' => AddressResource::make($this->whenLoaded('address')),
            'state' => StateResource::make($this->whenLoaded('state'))
        ];
    }
}
