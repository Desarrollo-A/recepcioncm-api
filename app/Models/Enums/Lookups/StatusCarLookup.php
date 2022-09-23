<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class StatusCarLookup
{
    const ACTIVE = 'Activo';
    const DOWN = 'Baja';
    const MAINTENANCE = 'Mantenimiento';

    public static function getAll(): Collection
    {
        return collect([self::ACTIVE, self::DOWN, self::MAINTENANCE]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
