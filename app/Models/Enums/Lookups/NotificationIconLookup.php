<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class NotificationIconLookup
{
    const ROOM = 'mat:meeting_room';
    const CONFIRM = 'mat:question_mark';

    public static function getAll(): Collection
    {
        return collect([self::ROOM, self::CONFIRM]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}