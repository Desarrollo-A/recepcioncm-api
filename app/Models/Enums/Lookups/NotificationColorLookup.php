<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class NotificationColorLookup
{
    const BLUE = '#2563eb';
    const GREEN = '#15803d';
    const ORANGE = '#ea580c';
    const RED = '#dc2626';
    const YELLOW = '#facc15';

    public static function getAll(): Collection
    {
        return collect([self::BLUE, self::GREEN, self::ORANGE, self::RED, self::YELLOW]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}