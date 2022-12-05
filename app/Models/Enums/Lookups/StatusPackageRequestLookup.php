<?php
namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class StatusPackageRequestLookup
{
    const NEW = 'Nueva';
    const APPROVED = 'Aprobada';
    const CANCELLED = 'Cancelada';
    const REJECTED = 'Rechazada';
    const DELIVERED = 'Entregado';
    const ROAD = 'En camino';
    const EXPIRED = 'Expirada';
    const PROPOSAL = 'Propuesta';
    const TRANSFER = 'Transferir';

    public static function getAll(): Collection
    {
        return collect([self::NEW, self::APPROVED, self::CANCELLED, self::REJECTED, self::DELIVERED, self::ROAD,
            self::EXPIRED, self::PROPOSAL, self::TRANSFER]);
    }

    public static function getAllCodes(): Collection
    {
        return collect([self::code(self::NEW), self::code(self::APPROVED), self::code(self::CANCELLED),
            self::code(self::REJECTED), self::code(self::DELIVERED), self::code(self::ROAD),
            self::code(self::EXPIRED), self::code(self::PROPOSAL), self::code(self::TRANSFER)]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
