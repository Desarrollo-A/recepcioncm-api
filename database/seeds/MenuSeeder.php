<?php

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Role;
use App\Models\Enums\NameRole;
use App\Models\Submenu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::all();
        $roleAdminId = $roles->firstWhere('name', '=', NameRole::ADMIN)->id;
        $roleApplicantId = $roles->firstWhere('name', '=', NameRole::APPLICANT)->id;
        $roleRecepcionistId = $roles->firstWhere('name', '=', NameRole::RECEPCIONIST)->id;
        $roleDriverId = $roles->firstWhere('name', '=', NameRole::DRIVER)->id;
        $roleManagerId = $roles->firstWhere('name', '=', NameRole::DEPARTMENT_MANAGER)->id;

        /***************************************************************************************/

        $this->createMenu('/dashboard/usuarios','Usuarios','mat:groups',1, $roleAdminId);
        $this->createMenu('/dashboard/oficinas','Oficinas','mat:apartment',2, $roleAdminId);

        /***************************************************************************************/

        $menu = $this->createMenu('/dashboard/solicitud','Reservaciones','mat:note_add',1, $roleApplicantId);
        $this->createSubmenu('/sala', 'Salas de Junta',1, $menu->id, $roleApplicantId);
        $this->createSubmenu('/paqueteria', 'Paquetería', 2, $menu->id, $roleApplicantId);
        $this->createSubmenu('/conductor', 'Chofer', 3, $menu->id, $roleApplicantId);
        $this->createSubmenu('/vehiculo', 'Vehículo', 4, $menu->id, $roleApplicantId);

        $menu = $this->createMenu('/dashboard/historial','Historial','mat:history',2, $roleApplicantId);
        $this->createSubmenu('/sala','Salas de Junta',1, $menu->id, $roleApplicantId);
        $this->createSubmenu('/paqueteria','Paquetería',2, $menu->id, $roleApplicantId);
        $this->createSubmenu('/conductor','Chofer',3, $menu->id, $roleApplicantId);
        $this->createSubmenu('/vehiculo', 'Vehículo', 4, $menu->id, $roleApplicantId);
        $this->createMenu('/dashboard/calendario','Calendario','mat:calendar_month',3, $roleApplicantId);

        /***************************************************************************************/

        $menu = $this->createMenu('/dashboard/solicitudes','Solicitudes','mat:history',1, $roleRecepcionistId);
        $this->createSubmenu('/sala','Salas de Junta',1, $menu->id, $roleRecepcionistId);
        $this->createSubmenu('/paqueteria','Paquetería',2, $menu->id, $roleRecepcionistId);
        $this->createSubmenu('/conductor','Chofer',3, $menu->id, $roleRecepcionistId);
        $this->createSubmenu('/vehiculo','Vehículo',4, $menu->id, $roleRecepcionistId);
        $this->createMenu('/dashboard/calendario','Calendario','mat:calendar_month',2, $roleRecepcionistId);
        $this->createMenu('/dashboard/inventario','Inventario','mat:inventory_2',2, $roleRecepcionistId);

        $menu = $this->createMenu('/dashboard/reporte','Reportes','mat:auto_graph',4, $roleRecepcionistId);
        $this->createSubmenu('/entrada-salida','Entradas/Salidas Inventario',1, $menu->id, $roleRecepcionistId);

        $menu = $this->createMenu('/dashboard/mantenimiento','Mantenimiento','mat:engineering',5, $roleRecepcionistId);
        $this->createSubmenu('/sala','Salas de Junta',1, $menu->id, $roleRecepcionistId);
        $this->createSubmenu('/auto','Vehículo',2, $menu->id, $roleRecepcionistId);
        $this->createSubmenu('/conductor','Chofer',3, $menu->id, $roleRecepcionistId);

        /***************************************************************************************/

        $menu = $this->createMenu('/dashboard/solicitudes-asignadas','Solicitudes','mat:assignment_turned_in',1, $roleDriverId);
        $this->createSubmenu('/paqueteria','Paquetería',1, $menu->id, $roleDriverId);
        $this->createSubmenu('/conductor', 'Chofer', 2, $menu->id, $roleDriverId);
        
        $menu = $this->createMenu('/dashboard/reporte','Reportes','mat:auto_graph',2, $roleDriverId);
        $this->createSubmenu('/paqueteria','Paquetería',1, $menu->id, $roleDriverId);

        /***************************************************************************************/

        $menu = $this->createMenu('/dashboard/director/solicitudes','Solicitudes','mat:assignment_turned_in',1, $roleManagerId);
        $this->createSubmenu('/paqueteria', 'Paquetería', 1, $menu->id, $roleManagerId);
    }

    /**
     * @param string $pathRoute
     * @param string $label
     * @param string $icon
     * @param int $number
     * @param int $roleId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    private function createMenu(string $pathRoute, string $label, string $icon, int $number, int $roleId) 
    {
        return Menu::query()->create([
            'path_route' => $pathRoute,
            'label' => $label,
            'icon' => $icon,
            'order' => $number,
            'role_id' => $roleId
        ]);
    }

    private function createSubmenu(string $pathRoute, string $label, int $order, int $menuId, int $roleId): void
    {
        Submenu::query()->create([
            'path_route' => $pathRoute,
            'label' => $label,
            'order' => $order,
            'menu_id' => $menuId,
            'role_id' => $roleId
        ]);
    }
}
