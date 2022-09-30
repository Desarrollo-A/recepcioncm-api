<?php

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Submenu;

class SubmenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $requestMenu = Menu::query()->where('path_route', '/dashboard/solicitud')->first()->id;
        $historyMenu = Menu::query()->where('path_route', '/dashboard/historial')->first()->id;
        $historyRecepcionistMenu = Menu::query()->where('path_route', '/dashboard/solicitudes')->first()->id;
        $mantoMenu = Menu::query()->where('path_route', '/dashboard/mantenimiento')->first()->id;

        Submenu::query()->create([
            'path_route' => '/sala',
            'label' => 'Salas de Junta',
            'order' => 1,
            'menu_id' => $requestMenu
        ]);

        Submenu::query()->create([
            'path_route' => '/auto',
            'label' => 'Automóvil',
            'order' => 2,
            'menu_id' => $requestMenu
        ]);

        Submenu::query()->create([
            'path_route' => '/conductor',
            'label' => 'Conductor',
            'order' => 3,
            'menu_id' => $requestMenu
        ]);

        Submenu::query()->create([
            'path_route' => '/sala',
            'label' => 'Salas de Junta',
            'order' => 1,
            'menu_id' => $historyMenu
        ]);

        Submenu::query()->create([
            'path_route' => '/auto',
            'label' => 'Automóvil',
            'order' => 2,
            'menu_id' => $historyMenu
        ]);

        Submenu::query()->create([
            'path_route' => '/conductor',
            'label' => 'Conductor',
            'order' => 3,
            'menu_id' => $historyMenu
        ]);

        Submenu::query()->create([
            'path_route' => '/sala',
            'label' => 'Salas de Junta',
            'order' => 1,
            'menu_id' => $historyRecepcionistMenu
        ]);

        Submenu::query()->create([
            'path_route' => '/auto',
            'label' => 'Automóvil',
            'order' => 2,
            'menu_id' => $historyRecepcionistMenu
        ]);

        Submenu::query()->create([
            'path_route' => '/conductor',
            'label' => 'Conductor',
            'order' => 3,
            'menu_id' => $historyRecepcionistMenu
        ]);

        Submenu::query()->create([
            'path_route' => '/sala',
            'label' => 'Salas de Junta',
            'order' => 1,
            'menu_id' => $mantoMenu
        ]);

        Submenu::query()->create([
            'path_route' => '/auto',
            'label' => 'Automóvil',
            'order' => 2,
            'menu_id' => $mantoMenu
        ]);
    }
}