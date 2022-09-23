<?php

namespace App\Models\Enums;

class TypeLookup
{
    const STATUS_USER = 1; // ESTATUS DEL USUARIO
    const SERVICES_LIST = 2; // SERVICIOS (REUNIÓN, CHOFER, AUTO)
    const STATUS_REQUEST = 3; // ESTATUS DE SOLICITUD (NUEVA, CANCELADA, EN REVISIÓN, ETC)
    const LEVEL_MEETING = 4; // TIPO DE REUNIÓN (ADMINISTRATIVA O DIRECTIVA)
    const INVENTORY_TYPE = 5; // TIPO DE INVENTARIO (Papelería, Botiquín, Limpieza, Cafetería)
    const UNIT_TYPE = 6; // UNIDAD DE MEDIDA (Pieza, Caja, Paquete, Kilo, Galón, Garrafa, Par, Bolsa, Bote),
    const STATUS_ROOM = 7; // (ACTIVA, BAJA, MANTENIMIENTO, ETC.)
    const REQUEST_TYPE_NOTIFICATIONS = 8; // (Sala, Automóvil, Conductor, Inventario, ETC.)
    const STATUS_CAR = 9; // (ACTIVO, BAJA, MANTENIMIENTO)
}