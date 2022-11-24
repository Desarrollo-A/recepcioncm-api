<?php

namespace App\Http\Resources\Address;

use App\Http\Resources\Lookup\LookupResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'street' => $this->street,
            'numExt' => $this->num_ext,
            'numInt' => $this->num_int,
            'suburb' => $this->suburb,
            'postalCode' => $this->postal_code,
            'state' => $this->state,
            'countryId' => $this->country_id,
            'country' => LookupResource::make($this->whenLoaded('country'))
        ];
    }
}
