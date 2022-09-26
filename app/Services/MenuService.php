<?php

namespace App\Services;

use App\Contracts\Repositories\MenuRepositoryInterface;
use App\Contracts\Repositories\SubmenuRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\MenuServiceInterface;
use App\Core\BaseService;
use App\Models\Enums\NameRole;
use App\Models\Enums\ViewsDefault;

class MenuService extends BaseService implements MenuServiceInterface
{
    protected $entityRepository;
    protected $submenuRepository;
    protected $userRepository;

    public function __construct(MenuRepositoryInterface $menuRepository,
                                SubmenuRepositoryInterface $submenuRepository,
                                UserRepositoryInterface $userRepository)
    {
        $this->entityRepository = $menuRepository;
        $this->submenuRepository = $submenuRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return void
     */
    public function createDefaultMenu(int $userId, string $role)
    {
        if ($role === NameRole::RECEPCIONIST) {
            $data = $this->createMenuAndSubmenuByRol(ViewsDefault::VIEWS_DEFAULT_RECEPCIONIST);
        } else if ($role === NameRole::APPLICANT) {
            $data = $this->createMenuAndSubmenuByRol(ViewsDefault::VIEWS_DEFAULT_APPLICANT);
        }

        $user = $this->userRepository->findById($userId);
        $user->menus()->attach($data['menus']);
        $user->submenus()->attach($data['submenus']);
    }

    private function createMenuAndSubmenuByRol(array $views): array
    {
        $menus = collect($views)
            ->map(function ($menu) {
                return $this->entityRepository->findByPathRoute($menu['path'])->id;
            })
            ->values();

        $submenus = collect($views)
            ->flatMap(function ($menu) {
                $menuId = $this->entityRepository->findByPathRoute($menu['path'])->id;
                return collect($menu['submenus'])
                    ->map(function ($submenu) use ($menuId) {
                        return $this->submenuRepository->findByPathRouteAndMenuId($submenu['path'], $menuId)->id;
                    });
            })
            ->values();

        return [
            'menus' => $menus,
            'submenus' => $submenus
        ];
    }
}