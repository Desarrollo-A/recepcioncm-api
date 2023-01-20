<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class StatusDriverRequestLookup
{
    const NEW = 'Nueva';
    const APPROVED = 'Aprobada';
    const CANCELLED = 'Cancelada';
    const REJECTED = 'Rechazada';
    const FINISHED = 'Terminada';
    const EXPIRED = 'Expirada';
    const PROPOSAL = 'Propuesta';
    const TRANSFER = 'Transferir';
    const ACCEPTED = 'Aceptada';

    public static function getAll(): Collection
    {
        return collect([self::NEW, self::APPROVED, self::CANCELLED, self::REJECTED, self::FINISHED, self::EXPIRED,
            self::PROPOSAL, self::TRANSFER, self::ACCEPTED]);
    }

    public static function getAllCodes(): Collection
    {
        return collect([self::code(self::NEW), self::code(self::APPROVED), self::code(self::CANCELLED),
            self::code(self::REJECTED), self::code(self::FINISHED), self::code(self::EXPIRED),
            self::code(self::PROPOSAL), self::code(self::TRANSFER), self::code(self::ACCEPTED)]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}