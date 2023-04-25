<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\MenuServiceInterface;
use App\Core\BaseApiController;
use App\Http\Requests\Navigation\SyncNavigationRequest;
use App\Http\Resources\Menu\NavigationMenuResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as Codes;

class MenuController extends BaseApiController
{
    private $menuService;

    public function __construct(MenuServiceInterface $menuService)
    {
        $this->middleware('role.permission:'.NameRole::ADMIN.','.NameRole::DEPARTMENT_MANAGER)
            ->only('getNavigationByUserId', 'syncNavigation');
        $this->middleware('role.permission:'.NameRole::allRolesMiddleware())
            ->only('hasPermissionToUrl');

        $this->menuService = $menuService;
    }

    public function getNavigationByUserId(int $userId): JsonResponse
    {
        $menu = $this->menuService->getNavigationByUserId($userId);
        return $this->showAll(NavigationMenuResource::collection($menu));
    }

    public function syncNavigation(int $userId, SyncNavigationRequest $request): Response
    {
        $data = $request->toDTO();
        $this->menuService->syncNavigation($userId, $data['menus'], $data['submenus']);
        return $this->noContentResponse();
    }

    public function hasPermissionToUrl(Request $request): JsonResponse
    {
        $pathUrl = $request->get('pathRoute');
        $result = $this->menuService->hasPermissionToUrl(auth()->id(), $pathUrl);
        return $this->successResponse(['hasPermission' => $result], Codes::HTTP_OK);
    }
}
