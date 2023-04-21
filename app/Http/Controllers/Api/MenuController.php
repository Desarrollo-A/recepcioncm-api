<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\MenuServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\Menu\NavigationMenuResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class MenuController extends BaseApiController
{
    private $menuService;

    public function __construct(MenuServiceInterface $menuService)
    {
        $this->middleware('role.permission:'.NameRole::ADMIN.','.NameRole::DEPARTMENT_MANAGER);

        $this->menuService = $menuService;
    }

    public function getNavigationByUserId(int $userId): JsonResponse
    {
        $menu = $this->menuService->getNavigationByUserId($userId);
        return $this->showAll(NavigationMenuResource::collection($menu));
    }
}
