<?php

namespace App\Http\Resources\Score;

use Illuminate\Http\Resources\Json\Resource;

class ScoreResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'requestId' => $this->request_id,
            'score' => $this->score,
            'comment' => $this->comment
        ];
    }
}
