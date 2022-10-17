<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class TypeRequestLookup
{
    const ROOM = 'Sala de junta';
    const TRAVEL = 'Traslados';
    const PAPER = 'Papelería';
    const PARCEL = 'Paquetería';

    public static function getAll(): Collection
    {
        return collect([self::ROOM, self::TRAVEL, self::PAPER, self::PARCEL]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}