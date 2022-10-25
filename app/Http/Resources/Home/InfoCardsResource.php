<?php

namespace App\Http\Resources\Home;

use Illuminate\Http\Resources\Json\Resource;

class InfoCardsResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'news' => $this['news'],
            'approved' => $this['approved'],
            'cancelled' => $this['cancelled'],
            'requests' => $this['requests']
        ];
    }
}
