<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class CountryAddressLookup
{
    const MEX = 'México';
    const EUA = 'Estados Unidos';

    public static function getAll(): Collection
    {
        return collect([self::MEX, self::EUA]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}