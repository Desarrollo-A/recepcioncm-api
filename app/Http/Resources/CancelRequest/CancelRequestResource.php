<?php

namespace App\Http\Resources\CancelRequest;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\Resource;

class CancelRequestResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'requestId' => $this->request_id,
            'cancelComment' => $this->cancel_comment,
            'userId' => $this->user_id,
            'user' => UserResource::make($this->whenLoaded('user'))
        ];
    }
}
