<?php

namespace App\Repositories;

use App\Contracts\Repositories\MenuRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class MenuRepository extends BaseRepository implements MenuRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Menu
     */
    protected $entity;

    public function __construct(Menu $menu)
    {
        $this->entity = $menu;
    }

    public function findByUserId(int $userId): Collection
    {
        return $this->entity
            ->select(['menus.*'])
            ->join('menu_user', 'menus.id', '=', 'menu_user.menu_id')
            ->where('menu_user.user_id', $userId)
            ->where('status', true)
            ->orderBy('order')
            ->get();
    }

    public function findAllByRoleId(int $roleId): Collection
    {
        return $this->entity
            ->where('role_id', $roleId)
            ->where('status', true)
            ->orderBy('order')
            ->get();
    }

    public function getPathRouteNavigationByUserId(int $userId): Collection
    {
        $submenusQuery = $this->entity
            ->selectRaw('CONCAT(m.path_route, s.path_route) AS path_route')
            ->from('submenus AS s')
            ->join('menus AS m', 's.menu_id', '=', 'm.id')
            ->join('submenu_user AS su', 'su.submenu_id', '=', 's.id')
            ->where('user_id', $userId);

        return $this->entity
            ->select(['m.path_route'])
            ->from('menus AS m')
            ->leftJoin('submenus AS s','s.menu_id', '=', 'm.id')
            ->join('menu_user AS mu', 'mu.menu_id', '=', 'm.id')
            ->whereNull('s.id')
            ->where('mu.user_id', $userId)
            ->unionAll($submenusQuery)
            ->get();
    }
}