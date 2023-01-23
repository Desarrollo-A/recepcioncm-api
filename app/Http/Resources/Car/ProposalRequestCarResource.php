<?php

namespace App\Http\Resources\Car;

use Illuminate\Http\Resources\Json\JsonResource;

class ProposalRequestCarResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this['id'],
            'trademark' => $this['trademark'],
            'model' => $this['model'],
            'color' => $this['color'],
            'licensePlate' => $this['license_plate'],
            'availableSchedules' => $this->availableSchedules($this['available_schedules']),
        ];
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
