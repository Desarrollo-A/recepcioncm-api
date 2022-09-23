<?php

namespace App\Http\Resources\Lookup;

use Illuminate\Http\Resources\Json\Resource;

class LookupResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'code' => $this->code,
            'name' => $this->name
        ];
    }
}
