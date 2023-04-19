<?php

namespace App\Http\Resources\DetailExternalParcel;

use Illuminate\Http\Resources\Json\JsonResource;

class DetailExternalParcelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'packageId' => $this->package_id,
            'companyName' => $this->company_name,
            'trackingCode' => $this->tracking_code,
            'urlTracking' => $this->url_tracking,
            'weight' => $this->weight
        ];
    }
}
