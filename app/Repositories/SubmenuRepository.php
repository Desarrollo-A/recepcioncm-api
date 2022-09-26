<?php

namespace App\Repositories;

use App\Contracts\Repositories\SubmenuRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Submenu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class SubmenuRepository extends BaseRepository implements SubmenuRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Submenu
     */
    protected $entity;

    public function __construct(Submenu $submenu)
    {
        $this->entity = $submenu;
    }

    public function findByUserId(int $userId): Collection
    {
        return $this->entity
            ->select(['submenus.*'])
            ->join('submenu_user', 'submenus.id', '=', 'submenu_user.submenu_id')
            ->where('submenu_user.user_id', $userId)
            ->get();
    }

    public function findByPathRouteAndMenuId(string $path, int $menuId): Submenu
    {
        return $this->entity
            ->where('path_route', $path)
            ->where('menu_id', $menuId)
            ->first();
    }
}