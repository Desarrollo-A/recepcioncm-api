<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class NotificationIconLookup
{
    const ROOM = 'mat:meeting_room';
    const CONFIRM = 'mat:question_mark';
    const WARNING = 'mat:warning';
    const STAR = 'mat:star';
    const TRUCK = 'mat:local_shipping';
    const BOX = 'mat:inventory_2';
    const DRIVER = 'mat:airline_seat_recline_normal';
    const CAR = 'mat:directions_car';

    public static function getAll(): Collection
    {
        return collect([self::ROOM, self::CONFIRM, self::WARNING, self::STAR, self::TRUCK, self::BOX, self::DRIVER, self::CAR]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}