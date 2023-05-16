<?php

namespace App\Http\Resources\DeliveredPackage;

use App\Helpers\Enum\Path;
use App\Helpers\File;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveredPackageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'packageId' => $this->package_id,
            'signature' => File::getExposedPath($this->signature, Path::PACKAGE_SIGNATURES),
            'observations' => $this->observations
        ];
    }
}
