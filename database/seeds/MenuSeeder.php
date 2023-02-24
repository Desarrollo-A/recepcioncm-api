<?php

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Menu::query()->create([
            'path_route' => '/dashboard/usuarios',
            'label' => 'Usuarios',
            'icon' => 'mat:groups',
            'order' => 1
        ]);

        Menu::query()->create([
            'path_route' => '/dashboard/solicitud',
            'label' => 'Reservaciones',
            'icon' => 'mat:note_add',
            'order' => 2
        ]);

        Menu::query()->create([
            'path_route' => '/dashboard/historial',
            'label' => 'Historial',
            'icon' => 'mat:history',
            'order' => 3
        ]);

        Menu::query()->create([
            'path_route' => '/dashboard/solicitudes',
            'label' => 'Solicitudes',
            'icon' => 'mat:history',
            'order' => 4
        ]);

        Menu::query()->create([
            'path_route' => '/dashboard/calendario',
            'label' => 'Calendario',
            'icon' => 'mat:calendar_month',
            'order' => 5
        ]);

        Menu::query()->create([
            'path_route' => '/dashboard/inventario',
            'label' => 'Inventario',
            'icon' => 'mat:inventory_2',
            'order' => 6
        ]);

        Menu::query()->create([
            'path_route' => '/dashboard/reporte',
            'label' => 'Reportes',
            'icon' => 'mat:auto_graph',
            'order' => 7
        ]);

        Menu::query()->create([
            'path_route' => '/dashboard/mantenimiento',
            'label' => 'Mantenimiento',
            'icon' => 'mat:engineering',
            'order' => 8
        ]);

        Menu::query()->create([
            'path_route' => '/dashboard/solicitudes-asignadas',
            'label' => 'Solicitudes',
            'icon' => 'mat:assignment_turned_in',
            'order' => 9
        ]);

        Menu::query()->create([
            'path_route' => '/dashboard/oficinas',
            'label' => 'Oficinas',
            'icon' => 'mat:apartment',
            'order' => 10
        ]);
    }
}
