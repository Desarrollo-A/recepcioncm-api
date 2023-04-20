<?php

namespace App\Services;

use App\Contracts\Repositories\MenuRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\SubmenuRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\MenuServiceInterface;
use App\Core\BaseService;

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

    /**
     * @return void
     */
    public function createDefaultMenu(int $userId, string $role)
    {
        $data = $this->createMenuAndSubmenuByRol($role);

        $user = $this->userRepository->findById($userId);
        $user->menus()->attach($data['menus']);
        $user->submenus()->attach($data['submenus']);
    }

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
}