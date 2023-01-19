<?php

namespace App\Http\Resources\Car;

use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Office\OfficeResource;
use App\Http\Resources\Util\StartDateEndDateResource;
use Illuminate\Http\Resources\Json\Resource;

class CarResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id ?? $this['id'],
            'businessName' => $this->business_name ?? $this['business_name'],
            'trademark' => $this->trademark ?? $this['trademark'],
            'model' => $this->model ?? $this['model'],
            'color' => $this->color ?? $this['color'],
            'licensePlate' => $this->license_plate ?? $this['license_plate'],
            'serie' => $this->serie ?? $this['serie'],
            'circulationCard' => $this->circulation_card ?? $this['circulation_card'],
            'people' => $this->people ?? $this['people'],
            'officeId' => $this->office_id ?? $this['office_id'],
            'statusId' => $this->status_id ?? $this['status_id'],
            'office' => OfficeResource::make($this->whenLoaded('office')),
            'status' => LookupResource::make($this->whenLoaded('status')),
            'drivers' => CarCollection::make($this->whenLoaded('drivers')),
            'availableSchedules' => $this->when(!is_null($this['available_schedules']),
                StartDateEndDateResource::collection($this['available_schedules'])),
        ];
    }
}
