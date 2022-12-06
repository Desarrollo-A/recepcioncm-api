<?php

namespace App\Http\Resources\Util;

use Illuminate\Http\Resources\Json\JsonResource;

class StartDateEndDateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'startDate' => $this->start_date->toDateTimeString(),
            'endDate' => $this->end_date->toDateTimeString(),
        ];
    }
}
