<?php

namespace App\Models\Enums;

class NameRole
{
    const ADMIN = 'Administrador';
    const RECEPCIONIST = 'Recepción';
    const APPLICANT = 'Solicitante';
    const DRIVER = 'Conductor';
    const DEPARTMENT_MANAGER = 'Dirección';

    static function allRolesMiddleware(): string
    {
        return self::ADMIN.','.self::RECEPCIONIST.','.self::APPLICANT.','.self::DRIVER.','.self::DEPARTMENT_MANAGER;
    }
}