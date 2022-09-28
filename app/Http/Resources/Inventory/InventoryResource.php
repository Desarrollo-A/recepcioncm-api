<?php

namespace App\Http\Resources\Inventory;

use App\Helpers\Enum\Path;
use App\Helpers\File;
use App\Http\Resources\InventoryRequest\InventoryRequestResource;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Office\OfficeResource;
use Illuminate\Http\Resources\Json\Resource;

class InventoryResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'trademark' => $this->trademark,
            'image' => File::getExposedPath($this->image, Path::INVENTORY_IMAGES),
            'stock' => $this->stock,
            'minimumStock' => $this->minimum_stock,
            'meeting' => $this->meeting,
            'status' => $this->status,
            'typeId' => $this->type_id,
            'unitId' => $this->unit_id,
            'officeId' => $this->office_id,
            'type' => LookupResource::make($this->whenLoaded('type')),
            'unit' => LookupResource::make($this->whenLoaded('unit')),
            'office' => OfficeResource::make($this->whenLoaded('office')),
            'inventoryRequest' => InventoryRequestResource::make($this->whenPivotLoaded('inventory_request', $this->pivot))
        ];
    }
}
