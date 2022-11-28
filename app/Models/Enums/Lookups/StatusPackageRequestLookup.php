<?php
namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class StatusPackageRequestLookup
{
    const NEW = 'Nueva';
    const APPROVED = 'Aprobada';
    const REJECTED = 'Rechazada';
    const DELIVERED = 'Entregado';
    const ROAD = 'En camino';

    public static function getAll(): Collection
    {
        return collect([self::NEW, self::APPROVED, self::REJECTED, self::DELIVERED, self::ROAD]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
