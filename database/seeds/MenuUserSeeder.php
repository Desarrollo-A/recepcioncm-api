<?php

use Illuminate\Database\Seeder;
use App\Models\Enums\ViewsDefault;
use App\Models\Menu;
use App\Models\Submenu;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Enums\NameRole;

class MenuUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menuAdmin = collect(ViewsDefault::VIEWS_DEFAULT_ADMIN)
            ->map(function ($menu) {
                return Menu::query()->where('path_route', $menu['path'])->first()->id;
            })
            ->values();

        $menuRecepcionist = collect(ViewsDefault::VIEWS_DEFAULT_RECEPCIONIST)
            ->map(function ($menu) {
                return Menu::query()->where('path_route', $menu['path'])->first()->id;
            })
            ->values();
        $submenuRecepcionist = collect(ViewsDefault::VIEWS_DEFAULT_RECEPCIONIST)
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

        $menuApplicant = collect(ViewsDefault::VIEWS_DEFAULT_APPLICANT)
            ->map(function ($menu) {
                return Menu::query()->where('path_route', $menu['path'])->first()->id;
            })
            ->values();
        $submenuApplicant = collect(ViewsDefault::VIEWS_DEFAULT_APPLICANT)
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
    }
}
