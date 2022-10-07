<?php

namespace App\Http\Resources\Calendar;

use App\Http\Resources\Request\RequestResource;
use Illuminate\Http\Resources\Json\Resource;

class SummaryOfDayResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'request' => RequestResource::make($this->request)
        ];
    }
}
