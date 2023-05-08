<?php

namespace App\Http\Resources\OfficeManager;

use Illuminate\Http\Resources\Json\JsonResource;

class OfficeManagerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'managerId' => $this->manager_id
        ];
    }
}
