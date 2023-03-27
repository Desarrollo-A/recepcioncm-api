<?php

namespace App\Http\Resources\HeavyShipping;

use Illuminate\Http\Resources\Json\JsonResource;

class HeavyShippingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'packageId' => $this->package_id,
            'high' => $this->high,
            'long' => $this->long,
            'width' => $this->width,
            'weight' => $this->weight,
            'description' => $this->description
        ];
    }
}
