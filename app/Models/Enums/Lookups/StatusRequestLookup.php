<?php

namespace App\Models\Enums\Lookups;

use Illuminate\Support\Collection;

class StatusRequestLookup
{
    const NEW = 'Nueva';
    const APPROVED = 'Aprobada';
    const REJECTED = 'Rechazada';
    const PROPOSAL = 'Propuesta';
    const CANCELLED = 'Cancelada';
    const WITHOUT_ATTENDING = 'Sin asistir';
    const FINISHED = 'Terminada';
    const IN_REVIEW = 'En revisión';
    const EXPIRED = 'Expirada';

    public static function getAll(): Collection
    {
        return collect([self::NEW, self::APPROVED, self::REJECTED, self::PROPOSAL, self::CANCELLED,
            self::WITHOUT_ATTENDING, self::FINISHED, self::IN_REVIEW, self::EXPIRED]);
    }

    public static function getAllCodes(): Collection
    {
        return collect([self::code(self::NEW), self::code(self::APPROVED), self::code(self::REJECTED),
            self::code(self::PROPOSAL), self::code(self::CANCELLED), self::code(self::WITHOUT_ATTENDING),
            self::code(self::FINISHED), self::code(self::IN_REVIEW), self::code(self::EXPIRED)]);
    }

    public static function code($const)
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = array_flip($class->getConstants());
        return $constants[$const];
    }
}
