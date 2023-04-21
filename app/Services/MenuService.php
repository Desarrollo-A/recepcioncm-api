<?php

namespace App\Services;

use App\Contracts\Repositories\MenuRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\SubmenuRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\MenuServiceInterface;
use App\Core\BaseService;
use App\Helpers\Utils;
use App\Models\Menu;
use App\Models\Submenu;
use Illuminate\Support\Collection;

class MenuService extends BaseService implements MenuServiceInterface
{
    protected $entityRepository;
    protected $submenuRepository;
    protected $userRepository;
    protected $roleRepository;

    public function __construct(
        MenuRepositoryInterface $menuRepository,
        SubmenuRepositoryInterface $submenuRepository,
        UserRepositoryInterface $userRepository,
        RoleRepositoryInterface $roleRepository
    )
    {
        $this->entityRepository = $menuRepository;
        $this->submenuRepository = $submenuRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function createDefaultMenu(int $userId, string $role): void
    {
        $data = $this->createMenuAndSubmenuByRol($role);

        $user = $this->userRepository->findById($userId);
        $user->menus()->attach($data['menus']);
        $user->submenus()->attach($data['submenus']);
    }

    /**
     * @param string $roleName
     * @return array ['menus' => array, 'submenus' => array]
     */
    private function createMenuAndSubmenuByRol(string $roleName): array
    {
        $roleId = $this->roleRepository->findByName($roleName)->id;

        $menus = $this->entityRepository
            ->findAllByRoleId($roleId)
            ->pluck('id')
            ->toArray();

        $submenus = $this->submenuRepository
            ->findAllByRoleId($roleId)
            ->pluck('id')
            ->toArray();

        return [
            'menus' => $menus,
            'submenus' => $submenus
        ];
    }

    public function getNavigationByUserId(int $userId): Collection
    {
        $user = $this->userRepository->findById($userId);

        $menus = $this->entityRepository->findAllByRoleId($user->role_id);
        $menusUser = $this->entityRepository->findByUserId($userId);
        $submenus = $this->submenuRepository->findAllByRoleId($user->role_id);
        $submenusUser = $this->submenuRepository->findByUserId($userId);

        return $menus->map(function (Menu $menu) use ($submenus, $menusUser, $submenusUser) {
            $submenusArr = $submenus
                ->filter(function (Submenu $submenu) use ($menu) {
                    return $submenu->menu_id === $menu->id;
                })
                ->map(function (Submenu $submenu) use ($submenusUser) {
                    $submenu['is_selected'] = $submenusUser->where('id', '=', $submenu->id)->count() === 1;
                    return $submenu;
                })
                ->values();

            $menu['is_selected'] = $menusUser->where('id', '=', $menu->id)->count() === 1;
            return collect($menu)->put('submenu', $submenusArr);
        });
    }
}