<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class TypeNotificationsLookup
{
    const ROOM = 'Sala';
    const CAR = 'Automóvil';
    const DRIVER = 'Conductor';
    const INVENTORY = 'Inventario';
    const GENERAL = 'General';

    public static function getAll(): Collection
    {
        return collect([self::ROOM, self::CAR, self::DRIVER, self::INVENTORY, self::GENERAL]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
