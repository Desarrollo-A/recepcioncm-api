<?php
namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class StatusPackageRequestLookup
{
    const NEW = 'Nueva';
    const APPROVED = 'Aprobada';
    const CANCELLED = 'Cancelada';
    const REJECTED = 'Rechazada';
    const FINISHED = 'Terminada';
    const ROAD = 'En camino';
    const EXPIRED = 'Expirada';
    const PROPOSAL = 'Propuesta';

    public static function getAll(): Collection
    {
        return collect([self::NEW, self::APPROVED, self::CANCELLED, self::REJECTED, self::FINISHED, self::ROAD,
            self::EXPIRED, self::PROPOSAL]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
