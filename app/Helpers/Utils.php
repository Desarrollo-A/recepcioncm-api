<?php

namespace App\Helpers;

use App\Events\AlertNotification;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\Notification;
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

    /**
     * @return void
     */
    public static function eventAlertNotification(Notification $notification)
    {
        $newNotification = $notification->fresh(['type', 'color', 'icon', 'requestNotification',
            'requestNotification.request', 'requestNotification.actionRequestNotification',
            'requestNotification.actionRequestNotification.type']);
        broadcast(new AlertNotification($notification->user_id, new NotificationResource($newNotification)));
    }

    public static function getDayName(int $day): string
    {
        if ($day === 0) {
            return 'Domingo';
        } else if ($day === 1) {
            return 'Lunes';
        } else if ($day === 2) {
            return 'Martes';
        } else if ($day === 3) {
            return 'Miércoles';
        } else if ($day === 4) {
            return 'Jueves';
        } else if ($day === 5) {
            return 'Viernes';
        } else if ($day === 5) {
            return 'Sábado';
        } else {
            return '';

        }
    }
}