<?php

namespace App\Models\Enums;

class TypeLookup
{
    const STATUS_USER = 1; // ESTATUS DEL USUARIO
    const TYPE_REQUEST = 2; // SERVICIOS (SALA DE JUNTAS, TRASLADOS, PAPELERÍA, ETC)
    const STATUS_ROOM_REQUEST = 3; // ESTATUS DE SOLICITUD (NUEVA, CANCELADA, EN REVISIÓN, ETC)
    const LEVEL_MEETING = 4; // TIPO DE REUNIÓN (ADMINISTRATIVA O DIRECTIVA)
    const INVENTORY_TYPE = 5; // TIPO DE INVENTARIO (Papelería, Botiquín, Limpieza, Cafetería)
    const UNIT_TYPE = 6; // UNIDAD DE MEDIDA (Pieza, Caja, Paquete, Kilo, Galón, Garrafa, Par, Bolsa, Bote),
    const STATUS_ROOM = 7; // (ACTIVA, BAJA, MANTENIMIENTO, ETC.)
    const REQUEST_TYPE_NOTIFICATIONS = 8; // (Sala, Automóvil, Conductor, Inventario, ETC.)
    const STATUS_CAR = 9; // (ACTIVO, BAJA, MANTENIMIENTO)
    const NOTIFICATION_COLOR = 10; // Color de la notificación
    const NOTIFICATION_ICON = 11; // Ícono de la notificación
    const ACTION_REQUEST_NOTIFICATION = 12; // Acciones de notificaciones
    const STATUS_PACKAGE_REQUEST = 13; //Estatus de solicitudes de paquetería
    const COUNTRY_ADDRESS = 14; // Listado de Países para las direcciones,
    const STATUS_DRIVER_REQUEST = 15; // Estatus de solicitudes de chofer
    const STATUS_CAR_REQUEST = 16; // Estatus de solicitudes de automóvil
}