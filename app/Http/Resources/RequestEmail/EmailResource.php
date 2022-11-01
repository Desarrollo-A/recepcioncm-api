<?php

namespace App\Http\Resources\RequestEmail;

use Illuminate\Http\Resources\Json\Resource;

class EmailResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'requestId' => $this->request_id
        ];
    }
}
