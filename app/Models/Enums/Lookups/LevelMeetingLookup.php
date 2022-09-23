<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class LevelMeetingLookup
{
    const ADMINISTRATIVE = 'Administrativa';
    const DIRECTIVE = 'Directiva';

    public static function getAll(): Collection
    {
        return collect([self::ADMINISTRATIVE, self::DIRECTIVE]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
