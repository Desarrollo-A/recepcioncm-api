<?php

namespace App\Http\Resources\ProposalRequest;

use Illuminate\Http\Resources\Json\Resource;

class ProposalRequestResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'requestId' => $this->request_id,
            'startDate' => $this->start_date->toDateTimeString(),
            'endDate' => is_null($this->end_date) ? null : $this->end_date->toDateTimeString()
        ];
    }
}
