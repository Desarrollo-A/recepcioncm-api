<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class UnitTypeLookup
{
    const PART = 'Pieza';
    const BOX = 'Caja';
    const PACKAGE = 'Paquete';
    const KILO = 'Kilo';
    const GALLON = 'Galón';
    const CARAFE = 'Garrafa';
    const PAIR = 'Par';
    const BAG = 'Bolsa';
    const CAN = 'Bote';

    public static function getAll(): Collection
    {
        return collect([self::PART, self::BOX, self::PACKAGE, self::KILO, self::GALLON, self::CARAFE, self::PAIR,
            self::BAG, self::CAN]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
