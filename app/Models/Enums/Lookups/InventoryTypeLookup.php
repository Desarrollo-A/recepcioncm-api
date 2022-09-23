<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class InventoryTypeLookup
{
    const STATIONERY = 'Papelería';
    const MEDICINE = 'Botiquín';
    const CLEANING = 'Limpieza';
    const COFFEE = 'Cafetería';

    public static function getAll(): Collection
    {
        return collect([self::STATIONERY, self::MEDICINE, self::CLEANING, self::COFFEE]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
