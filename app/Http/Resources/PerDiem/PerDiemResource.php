<?php

namespace App\Http\Resources\PerDiem;

use App\Http\Resources\File\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PerDiemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'requestId' => $this->request_id,
            'gasoline' => $this->gasoline,
            'tollbooths' => $this->tollbooths,
            'food' => $this->food,
            'spent' => $this->spent,
            'files' => FileResource::collection($this->whenLoaded('files'))
        ];
    }
}
