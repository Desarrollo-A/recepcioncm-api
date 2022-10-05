<?php

namespace App\Http\Resources\Request;

use App\Http\Resources\CancelRequest\CancelRequestResource;
use App\Http\Resources\Inventory\InventoryCollection;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\ProposalRequest\ProposalRequestResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\Resource;

class RequestResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'startDate' => $this->start_date->toDateTimeString(),
            'endDate' => $this->end_date->toDateTimeString(),
            'typeId' => $this->type_id,
            'comment' => $this->comment,
            'addGoogleCalendar' => $this->add_google_calendar,
            'people' => $this->people,
            'userId' => $this->user_id,
            'statusId' => $this->status_id,
            'status' => LookupResource::make($this->whenLoaded('status')),
            'type' => LookupResource::make($this->whenLoaded('type')),
            'user' => UserResource::make($this->whenLoaded('user')),
            'inventories' => InventoryCollection::make($this->whenLoaded('inventories')),
            'cancelRequest' => CancelRequestResource::make($this->whenLoaded('cancelRequest')),
            'proposalRequest' => ProposalRequestResource::collection($this->proposalRequest)
        ];
    }
}
