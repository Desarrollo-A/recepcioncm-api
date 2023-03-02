<?php

namespace App\Http\Resources\ProposalPackage;

use Illuminate\Http\Resources\Json\JsonResource;

class ProposalPackageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'packageId' => $this->package_id,
            'isDriverSelected' => $this->is_driver_selected
        ];
    }
}
