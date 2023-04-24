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
}