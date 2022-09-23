<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Resources\Json\Resource;

class RoleResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id ?? $this['id'],
            'name' => $this->name ?? $this['name'],
            'status' => $this->status ?? $this['status']
        ];
    }
}
