<?php

namespace App\Http\Resources\Home;

use Illuminate\Http\Resources\Json\Resource;

class HomeResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'cards' => InfoCardsResource::make($this['cards']),
            'last7DaysRequests' => $this['last7DaysRequests'],
            'totalMonth' => $this['totalMonth'],
            'percentage' => $this['percentage']
        ];
    }
}
