<?php

namespace App\Models\Enums;

class ViewsDefault
{
    const VIEWS_DEFAULT_ADMIN = [
        [
            'path' => '/dashboard/usuarios',
            'submenus' => []
        ],
        [
            'path' => '/dashboard/oficinas',
            'submenus' => []
        ]
    ];

    const VIEWS_DEFAULT_RECEPCIONIST = [
        [
            'path' => '/dashboard/solicitudes',
            'submenus' => [
                ['path' => '/sala'],
                ['path' => '/paqueteria'],
                ['path' => '/conductor'],
                ['path' => '/vehiculo']
            ]
        ],
        [
            'path' => '/dashboard/calendario',
            'submenus' => []
        ],
        [
            'path' => '/dashboard/inventario',
            'submenus' => []
        ],
        [
            'path' => '/dashboard/reporte',
            'submenus' => [
                ['path' => '/entrada-salida']
            ]
        ],
        [
            'path' => '/dashboard/mantenimiento',
            'submenus' => [
                ['path' => '/sala'],
                ['path' => '/auto'],
                ['path' => '/conductor']
            ]
        ]
    ];

    const VIEWS_DEFAULT_APPLICANT = [
        [
            'path' => '/dashboard/solicitud',
            'submenus' => [
                ['path' => '/sala'],
                ['path' => '/paqueteria'],
                ['path' => '/conductor'],
                ['path' => '/vehiculo']
            ]
        ],
        [
            'path' => '/dashboard/historial',
            'submenus' => [
                ['path' => '/sala'],
                ['path' => '/paqueteria'],
                ['path' => '/conductor'],
                ['path' => '/vehiculo']
            ]
        ],
        [
            'path' => '/dashboard/calendario',
            'submenus' => []
        ]
    ];

    const VIEWS_DEFAULT_DRIVER = [
        [
            'path' => '/dashboard/solicitudes-asignadas',
            'submenus' => [
                ['path' => '/paqueteria'],
                ['path' =>  '/conductor']
            ]
        ],
        [
            'path' => '/dashboard/reporte',
            'submenus' => [
                ['path' => '/paqueteria']
            ]
        ],
    ];
}