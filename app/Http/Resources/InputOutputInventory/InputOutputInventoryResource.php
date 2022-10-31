<?php

namespace App\Http\Resources\InputOutputInventory;

use Illuminate\Http\Resources\Json\Resource;

class InputOutputInventoryResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'quantity' => $this->sum_quantity,
            'cost' => $this->sum_cost,
            'type' => $this->type,
            'moveDate' => $this->move_date->toDateString()
        ];
    }
}
