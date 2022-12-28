<?php

use Illuminate\Database\Seeder;
use App\Models\Enums\ViewsDefault;
use App\Models\Menu;
use App\Models\Submenu;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Enums\NameRole;
use Illuminate\Support\Collection;

class MenuUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menuAdmin = $this->getMenu(ViewsDefault::VIEWS_DEFAULT_ADMIN);

        $menuRecepcionist = $this->getMenu(ViewsDefault::VIEWS_DEFAULT_RECEPCIONIST);
        $submenuRecepcionist = $this->getSubmenu(ViewsDefault::VIEWS_DEFAULT_RECEPCIONIST);

        $menuApplicant = $this->getMenu(ViewsDefault::VIEWS_DEFAULT_APPLICANT);
        $submenuApplicant = $this->getSubmenu(ViewsDefault::VIEWS_DEFAULT_APPLICANT);

        $menuDriver = $this->getMenu(ViewsDefault::VIEWS_DEFAULT_DRIVER);
        $submenuDriver = $this->getSubmenu(ViewsDefault::VIEWS_DEFAULT_DRIVER);

        User::query()
            ->with('menus')
            ->whereHas('role', function (Builder $query) {
                $query->where('name', NameRole::ADMIN);
            })
            ->get()
            ->map(function (User $user) use ($menuAdmin) {
                $user->menus()->attach($menuAdmin);
                return $user;
            });

        User::query()
            ->with('menus')
            ->whereHas('role', function (Builder $query) {
                $query->where('name', NameRole::RECEPCIONIST);
            })
            ->get()
            ->map(function (User $user) use ($menuRecepcionist, $submenuRecepcionist) {
                $user->menus()->attach($menuRecepcionist);
                $user->submenus()->attach($submenuRecepcionist);
                return $user;
            });

        User::query()
            ->with('menus')
            ->whereHas('role', function (Builder $query) {
                $query->where('name', NameRole::APPLICANT);
            })
            ->get()
            ->map(function (User $user) use ($menuApplicant, $submenuApplicant) {
                $user->menus()->attach($menuApplicant);
                $user->submenus()->attach($submenuApplicant);
                return $user;
            });

        User::query()
            ->with('menus')
            ->whereHas('role', function (Builder $query) {
                $query->where('name', NameRole::DRIVER);
            })
            ->get()
            ->map(function (User $user) use ($menuDriver, $submenuDriver) {
                $user->menus()->attach($menuDriver);
                $user->submenus()->attach($submenuDriver);
                return $user;
            });
    }

    private function getMenu(array $defaultMenu): Collection
    {
        return collect($defaultMenu)
            ->map(function ($menu) {
                return Menu::query()->where('path_route', $menu['path'])->first()->id;
            })
            ->values();
    }

    private function getSubmenu(array $defaultSubmenu): Collection
    {
        return collect($defaultSubmenu)
            ->flatMap(function ($menu) {
                $menuId = Menu::query()->where('path_route', $menu['path'])->first()->id;
                return collect($menu['submenus'])
                    ->map(function ($submenu) use ($menuId) {
                        return Submenu::query()
                            ->where('path_route', $submenu['path'])
                            ->where('menu_id', $menuId)
                            ->first()
                            ->id;
                    });
            })
            ->values();
    }
}
