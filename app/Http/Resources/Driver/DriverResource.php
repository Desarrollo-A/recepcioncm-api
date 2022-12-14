<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\Car\CarCollection;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Office\OfficeResource;
use Illuminate\Http\Resources\Json\Resource;

class DriverResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id'            =>  $this->id,
            'noEmployee'    =>  $this->no_employee,
            'fullName'      =>  $this->full_name,
            'email'         =>  $this->email,
            'personalPhone' =>  $this->personal_phone,
            'officePhone'   =>  $this->office_phone,
            'officeId'      =>  $this->office_id,
            'statusId'      =>  $this->status_id,
            'area'          =>  $this->area,
            'status'        =>  LookupResource::make($this->whenLoaded('status')),
            'office'        =>  OfficeResource::make($this->whenLoaded('office')),
            'cars'          =>  CarCollection::make($this->whenLoaded('cars'))
        ];
    }
}
