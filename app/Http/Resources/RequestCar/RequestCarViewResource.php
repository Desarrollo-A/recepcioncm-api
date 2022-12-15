<?php

namespace App\Http\Resources\RequestCar;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestCarViewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'requestId'     =>  $this->request_id,
            'code'          =>  $this->code,
            'title'         =>  $this->title,
            'startDate'     =>  $this->start_date->toDateTimeString(),
            'endDate'       =>  $this->end_date->toDateTimeString(),
            'statusName'    =>  $this->status_name,
            'statusCode'    =>  $this->status_code,
            'officeId'      =>  $this->office_id,
            'fullName'      =>  $this->full_name,
            'userId'        =>  $this->user_id,
            'requestCarId'  =>  $this->request_car_id
        ];
    }
}
