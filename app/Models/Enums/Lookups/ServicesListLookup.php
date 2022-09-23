<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class ServicesListLookup
{
    const ROOM = 'Sala de Juntas';
    const CAR = 'Automóvil';
    const DRIVER = 'Conductor';

    public static function getAll(): Collection
    {
        return collect([self::ROOM, self::CAR, self::DRIVER]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
