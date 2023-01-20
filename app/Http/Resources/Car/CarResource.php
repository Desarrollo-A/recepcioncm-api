<?php

namespace App\Http\Resources\Car;

use App\Http\Resources\Driver\DriverCollection;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Office\OfficeResource;
use Illuminate\Http\Resources\Json\Resource;

class CarResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'businessName' => $this->business_name,
            'trademark' => $this->trademark,
            'model' => $this->model,
            'color' => $this->color,
            'licensePlate' => $this->license_plate,
            'serie' => $this->serie,
            'circulationCard' => $this->circulation_card,
            'people' => $this->people,
            'officeId' => $this->office_id,
            'statusId' => $this->status_id,
            'office' => OfficeResource::make($this->whenLoaded('office')),
            'status' => LookupResource::make($this->whenLoaded('status')),
            'drivers' => DriverCollection::make($this->whenLoaded('drivers')),
        ];
    }
}
