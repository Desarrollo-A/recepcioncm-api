<?php

namespace App\Http\Resources\Package;

use App\Helpers\Enum\Path;
use App\Helpers\File;
use App\Http\Resources\Address\AddressResource;
use App\Http\Resources\Request\RequestResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray($request): array
    {
        info(!is_null($this->authorization_filename));
        info(!empty($this->authorization_filename));
        return [
            'id' => $this->id,
            'authorizationFilename' => is_null($this->authorization_filename)
                ? ''
                : File::getExposedPath($this->authorization_filename, Path::PACKAGE_AUTHORIZATION_DOCUMENTS),
            'nameReceive' => $this->name_receive,
            'emailReceive' => $this->email_receive,
            'commentReceive' => $this->comment_receive,
            'pickupAddressId' => $this->pickup_address_id,
            'arrivalAddressId' => $this->arrival_address_id,
            'requestId' => $this->request_id,
            'officeId' => $this->office_id,
            'trackingCode' => $this->tracking_code,
            'pickupAddress' => AddressResource::make($this->whenLoaded('pickupAddress')),
            'arrivalAddress' => AddressResource::make($this->whenLoaded('arrivalAddress')),
            'request' => RequestResource::make($this->whenLoaded('request'))
        ];
    }
}
