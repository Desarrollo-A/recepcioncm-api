<?php

namespace App\Http\Resources\File;

use App\Helpers\Enum\Path;
use App\Helpers\File;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'filename' => File::getExposedPath($this->filename, Path::FILES)
        ];
    }
}
