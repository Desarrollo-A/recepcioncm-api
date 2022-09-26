<?php

namespace App\Http\Resources\Room;

use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Office\OfficeResource;
use Illuminate\Http\Resources\Json\Resource;

class RoomResource extends Resource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'officeId' => $this->office_id,
            'noPeople' => $this->no_people,
            'recepcionistId' => $this->recepcionist_id,
            'statusId' => $this->status_id,
            'office' => OfficeResource::make($this->whenLoaded('office')),
            'status' => LookupResource::make($this->whenLoaded('status'))
        ];
    }
}

