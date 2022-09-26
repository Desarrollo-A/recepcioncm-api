<?php

namespace App\Http\Resources\ProposalRequest;

use Illuminate\Http\Resources\Json\Resource;

class ProposalRequestResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'requestId' => $this->request_id,
            'startDate' => $this->start_date->toDateTimeString(),
            'endDate' => $this->end_date->toDateTimeString()
        ];
    }
}
