<?php

namespace App\Helpers;

use Carbon\Carbon;

class Utils
{
    const START_WORKING_HOUR = 8;
    const FINISH_WORKING_HOUR = 18;

    /**
     * @param string $date
     * @param int|float $duration
     * @return array
     */
    public static function getAvailableSchedule(string $date, $duration): array
    {
        $schedule = [];
        $condition = true;
        $datetimeReference = new Carbon("$date 0".self::START_WORKING_HOUR.':00:00');

        if (is_int($duration)) {
            while($condition) {
                $dateRef = new Carbon($datetimeReference);
                $startDate = new Carbon($datetimeReference);
                $endDate = new Carbon($dateRef->addHours($duration));

                $schedule[] = [
                    'start_time' => $startDate,
                    'end_time' => $endDate
                ];

                if (intval($endDate->format('H')) === self::FINISH_WORKING_HOUR) {
                    $condition = false;
                } else {
                    $datetimeReference->addMinutes(30);
                }
            }
        }

        if (is_float($duration)) {
            $hours = $duration - .5;
            while($condition) {
                $dateRef = new Carbon($datetimeReference);
                $startDate = new Carbon($datetimeReference);
                $endDate = new Carbon($dateRef->addHours($hours)->addMinutes(30));

                $schedule[] = [
                    'start_time' => $startDate,
                    'end_time' => $endDate
                ];

                if (intval($endDate->format('H')) === self::FINISH_WORKING_HOUR) {
                    $condition = false;
                } else {
                    $datetimeReference->addMinutes(30);
                }
            }
        }

        return $schedule;
    }
}