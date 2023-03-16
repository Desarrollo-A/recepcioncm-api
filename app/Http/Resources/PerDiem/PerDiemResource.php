<?php

namespace App\Http\Resources\PerDiem;

use App\Helpers\Enum\Path;
use App\Helpers\File;
use Illuminate\Http\Resources\Json\JsonResource;

class PerDiemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'requestId' => $this->request_id,
            'gasoline' => $this->gasoline,
            'tollbooths' => $this->tollbooths,
            'food' => $this->food,
            'billFilename' => is_null($this->bill_filename)
                ? null
                : File::getExposedPath($this->bill_filename, Path::REQUEST_BILL_ZIP),
            'spent' => $this->spent
        ];
    }
}
