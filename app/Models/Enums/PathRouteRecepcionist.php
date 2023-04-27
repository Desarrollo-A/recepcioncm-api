<?php

namespace App\Models\Enums;

class PathRouteRecepcionist
{
    const PATH_ROUTE_HISTORY_BASE = '/dashboard/historial';
    const ROOM = '/sala';
    const DRIVER = '/chofer';
    const CAR = '/vehiculo';
    const PARCEL = '/paqueteria';
    const INVENTORY = '/dashboard/inventario';

    static function fullPathHistory(string $path): string
    {
        return self::PATH_ROUTE_HISTORY_BASE.$path;
    }
}