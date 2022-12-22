<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class TypeNotificationsLookup
{
    const ROOM = 'Sala';
    const DRIVER = 'Chofer';
    const CAR = 'Automóvil';
    const INVENTORY = 'Inventario';
    const PAPER = 'Papelería';
    const PARCEL = 'Paquetería';
    const GENERAL = 'General';

    public static function getAll(): Collection
    {
        return collect([self::ROOM, self::DRIVER, self::CAR, self::INVENTORY, self::PAPER, self::PARCEL, self::GENERAL]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
