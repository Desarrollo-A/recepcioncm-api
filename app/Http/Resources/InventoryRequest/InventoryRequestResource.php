<?php

namespace App\Http\Resources\InventoryRequest;

use Illuminate\Http\Resources\Json\Resource;

class InventoryRequestResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'requestId' => $this->request_id,
            'inventoryId' => $this->inventory_id,
            'quantity' => $this->quantity,
            'applied' => $this->applied,
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
