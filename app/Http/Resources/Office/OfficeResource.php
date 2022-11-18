<?php

namespace App\Http\Resources\Office;

use App\Http\Resources\State\StateResource;
use Illuminate\Http\Resources\Json\Resource;

class OfficeResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'state' => StateResource::make($this->whenLoaded('state'))
        ];
    }
}
