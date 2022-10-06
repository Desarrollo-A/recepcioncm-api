<?php

namespace App\Http\Resources\Calendar;

use App\Http\Resources\Request\RequestResource;
use Illuminate\Http\Resources\Json\Resource;

class CalendarResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'title' => $this->title,
            'request' => RequestResource::make($this->request)
        ];
    }
}
