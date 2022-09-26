<?php

namespace App\Http\Resources\RequestRoom;

use Illuminate\Http\Resources\Json\Resource;

class RequestRoomResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
