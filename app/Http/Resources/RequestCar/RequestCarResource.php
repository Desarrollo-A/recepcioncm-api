<?php

namespace App\Http\Resources\RequestCar;

use App\Helpers\Enum\Path;
use App\Helpers\File;
use App\Http\Resources\CarRequestSchedule\CarRequestScheduleResource;
use App\Http\Resources\Office\OfficeResource;
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
            'requestId' => $this->request_id,
            'officeId' => $this->office_id,
            'initialKm' => $this->initial_km,
            'finalKm' => $this->final_km,
            'deliveryCondition' => $this->delivery_condition,
            'imageZip' => is_null($this->image_zip)
                ? null
                : File::getExposedPath($this->image_zip, Path::REQUEST_CAR_IMAGES),
            'request' => RequestResource::make($this->whenLoaded('request')),
            'carRequestSchedule' => CarRequestScheduleResource::make($this->whenLoaded('carRequestSchedule')),
            'office' => OfficeResource::make($this->whenLoaded('office')),
        ];
    }
}
