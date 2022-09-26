<?php

namespace App\Http\Resources\State;

use Illuminate\Http\Resources\Json\Resource;

class StateResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status
        ];
    }
}
