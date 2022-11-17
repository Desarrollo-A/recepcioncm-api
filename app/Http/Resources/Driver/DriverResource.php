<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Office\OfficeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
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
            'status'        =>  LookupResource::make($this->whenLoaded('status')),
            'office'        =>  OfficeResource::make($this->whenLoaded('office'))
        ];
    }
}
