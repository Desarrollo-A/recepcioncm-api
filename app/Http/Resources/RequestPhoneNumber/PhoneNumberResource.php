<?php

namespace App\Http\Resources\RequestPhoneNumber;

use Illuminate\Http\Resources\Json\Resource;

class PhoneNumberResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'requestId' => $this->request_id
        ];
    }
}
