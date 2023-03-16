<?php

namespace App\Http\Resources\Request;

use App\Http\Resources\CancelRequest\CancelRequestResource;
use App\Http\Resources\Inventory\InventoryCollection;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\PerDiem\PerDiemResource;
use App\Http\Resources\ProposalRequest\ProposalRequestResource;
use App\Http\Resources\RequestEmail\EmailResource;
use App\Http\Resources\RequestPhoneNumber\PhoneNumberResource;
use App\Http\Resources\RequestRoom\RequestRoomResource;
use App\Http\Resources\Score\ScoreResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\Resource;

class RequestResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'startDate' => $this->start_date->toDateTimeString(),
            'endDate' => is_null($this->end_date) ? null : $this->end_date->toDateTimeString(),
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
            'requestPhoneNumber' => PhoneNumberResource::collection($this->whenLoaded('requestPhoneNumber')),
            'requestEmail' => EmailResource::collection($this->whenLoaded('requestEmail')),
            'proposalRequest' => ProposalRequestResource::collection($this->proposalRequest),
            'requestRoom' => RequestRoomResource::make($this->whenLoaded('requestRoom')),
            'score' => ScoreResource::make($this->whenLoaded('score')),
            'perDiem' => PerDiemResource::make($this->whenLoaded('perDiem')),
        ];
    }
}
