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
            ->get();
    }

    public function findByPathRoute(string $path): Menu
    {
        return $this->entity->where('path_route', $path)->firstOrFail();
    }
}