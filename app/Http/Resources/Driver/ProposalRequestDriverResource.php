<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class ProposalRequestDriverResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this['id'],
            'noEmployee' => $this['no_employee'],
            'fullName' => $this['full_name'],
            'officeId' => $this['office_id'],
            'availableCars' => $this->availableCars($this['available_cars']),
        ];
    }

    private function availableCars(array $availableCars): array
    {
        $cars = [];
        foreach($availableCars as $car) {
            $cars[] = [
                'id' => $car['id'],
                'trademark' => $car['trademark'],
                'model' => $car['model'],
                'color' => $car['color'],
                'licensePlate' => $car['license_plate'],
                'availableSchedules' => $this->availableSchedules($car['available_schedules']),
            ];
        }

        return $cars;
    }

    private function availableSchedules(array $availableSchedules): array
    {
        $schedules = [];
        foreach($availableSchedules as $availableSchedule) {
            $schedules[] = [
                'startDate' => $availableSchedule['start_time']->toDateTimeString(),
                'endDate' => $availableSchedule['end_time']->toDateTimeString(),
            ];
        }

        return $schedules;
    }
}
