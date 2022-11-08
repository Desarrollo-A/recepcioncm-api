<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class ActionRequestNotificationLookup
{
    const CONFIRM = 'Confirmar notificación';
    const SCORE = 'Calificar servicio';

    public static function getAll(): Collection
    {
        return collect([self::CONFIRM, self::SCORE]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}