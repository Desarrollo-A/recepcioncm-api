<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class NotificationIconLookup
{
    const ROOM = 'mat:meeting_room';

    public static function getAll(): Collection
    {
        return collect([self::ROOM]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}