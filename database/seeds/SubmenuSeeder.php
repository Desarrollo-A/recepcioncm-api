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
        $reportMenu = Menu::query()->where('path_route', '/dashboard/reporte')->first()->id;
        $requestAssignsMenu = Menu::query()->where('path_route', '/dashboard/solicitudes-asignadas')->first()->id;

        $this->createSubmenu('/sala', 'Salas de Junta',1, $requestMenu);
        $this->createSubmenu('/paqueteria', 'Paquetería', 2, $requestMenu);
        $this->createSubmenu('/conductor', 'Chofer', 3, $requestMenu);
        $this->createSubmenu('/vehiculo', 'Vehículo', 4, $requestMenu);

        $this->createSubmenu('/sala','Salas de Junta',1, $historyMenu);
        $this->createSubmenu('/paqueteria','Paquetería',2, $historyMenu);
        $this->createSubmenu('/conductor','Chofer',3, $historyMenu);
        $this->createSubmenu('/vehiculo', 'Vehículo', 4, $historyMenu);

        $this->createSubmenu('/sala','Salas de Junta',1, $historyRecepcionistMenu);
        $this->createSubmenu('/paqueteria','Paquetería',2, $historyRecepcionistMenu);
        $this->createSubmenu('/conductor','Chofer',3, $historyRecepcionistMenu);
        $this->createSubmenu('/vehiculo','Vehículo',4, $historyRecepcionistMenu);

        $this->createSubmenu('/sala','Salas de Junta',1, $mantoMenu);
        $this->createSubmenu('/auto','Vehículo',2, $mantoMenu);
        $this->createSubmenu('/conductor','Chofer',3, $mantoMenu);

        $this->createSubmenu('/entrada-salida','Entradas/Salidas Inventario',1, $reportMenu);
        $this->createSubmenu('/paqueteria','Paquetería',2, $reportMenu);

        $this->createSubmenu('/paqueteria','Paquetería',1, $requestAssignsMenu);
        $this->createSubmenu('/conductor', 'Chofer', 2, $requestAssignsMenu);
    }

    private function createSubmenu(string $pathRoute, string $label, int $order, int $menuId): void
    {
        Submenu::query()->create([
            'path_route' => $pathRoute,
            'label' => $label,
            'order' => $order,
            'menu_id' => $menuId
        ]);
    }
}
