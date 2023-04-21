<?php

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Submenu;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Enums\NameRole;
use App\Models\Role;

class MenuUserSeeder extends Seeder
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

        $menuAdmin = $this->getMenu($roleAdminId);
        $submenuAdmin = $this->getSubmenu($roleAdminId);

        $menuRecepcionist = $this->getMenu($roleRecepcionistId);
        $submenuRecepcionist = $this->getSubmenu($roleRecepcionistId);

        $menuApplicant = $this->getMenu($roleApplicantId);
        $submenuApplicant = $this->getSubmenu($roleApplicantId);

        $menuDriver = $this->getMenu($roleDriverId);
        $submenuDriver = $this->getSubmenu($roleDriverId);

        $menuManager = $this->getMenu($roleManagerId);
        $submenuManager = $this->getSubmenu($roleManagerId);

        $this->attachMenuSubmenu(NameRole::ADMIN, $menuAdmin, $submenuAdmin);
        $this->attachMenuSubmenu(NameRole::RECEPCIONIST, $menuRecepcionist, $submenuRecepcionist);
        $this->attachMenuSubmenu(NameRole::APPLICANT, $menuApplicant, $submenuApplicant);
        $this->attachMenuSubmenu(NameRole::DRIVER, $menuDriver, $submenuDriver);
        $this->attachMenuSubmenu(NameRole::DEPARTMENT_MANAGER, $menuManager, $submenuManager);
    }

    /**
     * @param int[] $menu
     * @param int[] $submenu
     */
    private function attachMenuSubmenu(string $roleName, array $menu, array $submenu): void
    {
        User::query()
            ->whereHas('role', function (Builder $query) use ($roleName) {
                $query->where('name', $roleName);
            })
            ->get()
            ->each(function (User $user) use ($menu, $submenu) {
                $user->menus()->attach($menu);
                $user->submenus()->attach($submenu);
            });
    }

    private function getMenu(int $roleId)
    {
        return Menu::query()
            ->where('role_id', $roleId)
            ->pluck('id')
            ->toArray();
    }

    private function getSubmenu(int $roleId): array
    {
        return Submenu::query()
            ->where('role_id', $roleId)
            ->pluck('id')
            ->toArray();
    }
}
