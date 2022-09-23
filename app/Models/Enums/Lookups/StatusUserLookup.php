<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class StatusUserLookup
{
    const ACTIVE = 'Activo';
    const INACTIVE = 'Inactivo';
    const BLOCKED = 'Bloqueado';

    public static function getAll(): Collection
    {
        return collect([self::ACTIVE, self::INACTIVE, self::BLOCKED]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}