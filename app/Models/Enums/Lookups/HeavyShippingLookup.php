<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class HeavyShippingLookup
{
    const NUMBER = 1;

    public static function getAll(): Collection
    {
        return collect([self::NUMBER]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}