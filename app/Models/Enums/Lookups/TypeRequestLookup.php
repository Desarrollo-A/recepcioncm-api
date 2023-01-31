<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class TypeRequestLookup
{
    const ROOM = 'Sala de junta';
    const DRIVER = 'Chofer';
    const CAR = 'Vehículo';
    const PAPER = 'Papelería';
    const PARCEL = 'Paquetería';

    public static function getAll(): Collection
    {
        return collect([self::ROOM, self::DRIVER, self::CAR, self::PAPER, self::PARCEL]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
