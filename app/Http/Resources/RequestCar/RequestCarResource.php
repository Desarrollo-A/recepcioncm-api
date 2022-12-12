<?php

namespace App\Http\Resources\RequestCar;

use App\Helpers\Enum\Path;
use App\Helpers\File;
use App\Http\Resources\Request\RequestResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestCarResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'authorizationFilename' => is_null($this->authorization_filename)
                ? null
                : File::getExposedPath($this->authorization_filename, Path::CAR_AUTHORIZATION_DOCUMENTS),
            'responsiveFilename' => is_null($this->responsive_filename)
                ? null
                : File::getExposedPath($this->responsive_filename, Path::CAR_AUTHORIZATION_DOCUMENTS),
            'requestId' => $this->request_id,
            'officeId' => $this->office_id,
            'request' => RequestResource::make($this->whenLoaded('request'))
        ];
    }
}
