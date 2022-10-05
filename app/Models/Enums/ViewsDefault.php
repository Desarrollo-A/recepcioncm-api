<?php

namespace App\Models\Enums;

class ViewsDefault
{
    const VIEWS_DEFAULT_ADMIN = [
        [
            'path' => '/dashboard/usuarios',
            'submenus' => []
        ]
    ];

    const VIEWS_DEFAULT_RECEPCIONIST = [
        [
            'path' => '/dashboard/solicitudes',
            'submenus' => [
                ['path' => '/sala']
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
            'submenus' => []
        ],
        [
            'path' => '/dashboard/mantenimiento',
            'submenus' => [
                ['path' => '/sala'],
                ['path' => '/auto']
            ]
        ]
    ];

    const VIEWS_DEFAULT_APPLICANT = [
        [
            'path' => '/dashboard/solicitud',
            'submenus' => [
                ['path' => '/sala']
            ]
        ],
        [
            'path' => '/dashboard/historial',
            'submenus' => [
                ['path' => '/sala']
            ]
        ],
        [
            'path' => '/dashboard/calendario',
            'submenus' => []
        ]
    ];
}