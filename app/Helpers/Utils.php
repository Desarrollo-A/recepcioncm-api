<?php

namespace App\Helpers;

use App\Events\AlertNotification;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\Enums\Lookups\StatusCarRequestLookup;
use App\Models\Enums\Lookups\StatusDriverRequestLookup;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Menu;
use App\Models\Notification;
use App\Models\Request;
use App\Models\Submenu;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as CollectionEloquent;

class Utils
{
    const START_WORKING_HOUR = 8;
    const FINISH_WORKING_HOUR = 18;

    /**
     * @param string $date
     * @param int|float $duration
     * @return array
     */
    public static function getAvailableRoomSchedule(string $date, $duration): array
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
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|float $duration
     * @return array
     */
    public static function getAvailableProposalDriverCarSchedule(Carbon $startDate, Carbon $endDate, $duration): array
    {
        $schedule = [];
        $condition = true;
        $datetimeReference = new Carbon($startDate->toDateString());
        $endDatetimeReference = new Carbon("{$endDate->toDateString()} 23:30:00");

        if (is_int($duration)) {
            while($condition) {
                $dateRef = new Carbon($datetimeReference);
                $startDate = new Carbon($datetimeReference);
                $endDate = new Carbon($dateRef->addHours($duration));

                $schedule[] = [
                    'start_time' => $startDate,
                    'end_time' => $endDate
                ];

                if ($endDate->eq($endDatetimeReference)) {
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

                if ($endDate->eq($endDatetimeReference)) {
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
        $newNotification = $notification->fresh([
            'type',
            'color',
            'icon',
            'requestNotification',
            'requestNotification.request',
            'requestNotification.actionRequestNotification',
            'requestNotification.actionRequestNotification.type'
        ]);
        broadcast(new AlertNotification($notification->user_id, new NotificationResource($newNotification)));
    }

    public static function getDayName(int $day): string
    {
        if ($day === 0) {
            return 'Domingo';
        }
        if ($day === 1) {
            return 'Lunes';
        }
        if ($day === 2) {
            return 'Martes';
        }
        if ($day === 3) {
            return 'Miércoles';
        }
        if ($day === 4) {
            return 'Jueves';
        }
        if ($day === 5) {
            return 'Viernes';
        }
        if ($day === 6) {
            return 'Sábado';
        }

        return '';
    }

    public static function getStatusApprovedRequest(): array
    {
        return [
            StatusRoomRequestLookup::code(StatusRoomRequestLookup::APPROVED),
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED),
            StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED),
            StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED)
        ];
    }

    public static function getStatusNewsRequest(): array
    {
        return [
            StatusRoomRequestLookup::code(StatusRoomRequestLookup::NEW),
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW),
            StatusDriverRequestLookup::code(StatusDriverRequestLookup::NEW),
            StatusCarRequestLookup::code(StatusCarRequestLookup::NEW)
        ];
    }

    public static function getStatusCancelledRequests(): array
    {
        return [
            StatusRoomRequestLookup::code(StatusRoomRequestLookup::CANCELLED),
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED),
            StatusDriverRequestLookup::code(StatusDriverRequestLookup::CANCELLED),
            StatusCarRequestLookup::code(StatusCarRequestLookup::CANCELLED)
        ];
    }

    public static function getAllTypesRequest(): array
    {
        return [
            TypeRequestLookup::code(TypeRequestLookup::ROOM),
            TypeRequestLookup::code(TypeRequestLookup::PARCEL),
            TypeRequestLookup::code(TypeRequestLookup::DRIVER),
            TypeRequestLookup::code(TypeRequestLookup::CAR)
        ];
    }

    public static function getAllExpiredStatusRequest(): array
    {
        return [
            StatusRoomRequestLookup::code(StatusRoomRequestLookup::NEW),
            StatusRoomRequestLookup::code(StatusRoomRequestLookup::PROPOSAL),
            StatusRoomRequestLookup::code(StatusRoomRequestLookup::IN_REVIEW),

            StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW),
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::PROPOSAL),
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::IN_REVIEW),

            StatusDriverRequestLookup::code(StatusDriverRequestLookup::NEW),
            StatusDriverRequestLookup::code(StatusDriverRequestLookup::PROPOSAL),

            StatusCarRequestLookup::code(StatusCarRequestLookup::NEW),
            StatusCarRequestLookup::code(StatusCarRequestLookup::PROPOSAL)
        ];
    }

    public static function createSummaryOfDayObject(string $title, string $subtitle, Request $request): object
    {
        return (object)['title' => $title, 'subtitle' => $subtitle, 'request' => $request];
    }

    public static function createEventCalendarObject(string $title, Request $request): object
    {
        return (object)['title' => $title, 'request' => $request];
    }

    public static function convertErrorMessageToStringArray(array $errors): array
    {
        $convertErrorsArray = [];

        foreach ($errors as $error) {
            foreach ($error as $row) {
                $convertErrorsArray[] = $row;
            }
        }

        return $convertErrorsArray;
    }

    public static function convertErrorMessageToCollectionExcel(array $errors): Collection
    {
        $data = [];

        foreach ($errors as $i => $error) {
            foreach ($error as $row) {
                $data[] = [
                    'Línea' => $i,
                    'Error' => $row
                ];
            }
        }

        return collect($data);
    }

    public static function convertNavigationMenu(CollectionEloquent $menus, CollectionEloquent $submenus): Collection
    {
        return $menus->map(function (Menu $menu) use ($submenus) {
            $submenusArr = $submenus
                ->filter(function (Submenu $submenu) use ($menu) {
                    return $submenu->menu_id === $menu->id;
                })
                ->map(function (Submenu $submenu) use ($menu) {
                    $submenu['path_route'] = $menu['path_route'].$submenu['path_route'];
                    return $submenu;
                })
                ->values();

            return collect($menu)->put('submenu', $submenusArr);
        });
    }

    public static function generateDaysArray(Carbon $startDate, Carbon $endDate): array
    {
        $firstDate = Carbon::make($startDate->toDateString());
        $lastDate = Carbon::make($endDate->toDateString());

        if ($firstDate->eq($lastDate)) {
            return [$firstDate->dayOfWeek];
        }

        $daysOfWeek = [];
        while($firstDate->lessThanOrEqualTo($lastDate)) {
            $daysOfWeek[] = $firstDate->dayOfWeek;
            $firstDate->addDays(1);
        }

        return $daysOfWeek;
    }
}