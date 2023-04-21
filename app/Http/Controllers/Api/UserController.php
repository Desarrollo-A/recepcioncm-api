<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\LookupServiceInterface;
use App\Contracts\Services\MenuServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\User\BulkStoreDriverRequest;
use App\Http\Requests\User\ChangeStatusUserRequest;
use App\Http\Requests\User\StoreDriverRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserChRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends BaseApiController
{
    private $userService;
    private $menuService;
    private $lookupService;

    public function __construct(
        UserServiceInterface $userService,
        MenuServiceInterface $menuService,
        LookupServiceInterface $lookupService
    )
    {
        $this->middleware('role.permission:'.NameRole::ADMIN)
            ->only('index', 'show', 'changeStatus', 'update', 'findAllDepartmentManagers');
        $this->middleware('role.permission:'.NameRole::allRolesMiddleware())
            ->only('showProfile');
        $this->middleware('role.permission:'.NameRole::DEPARTMENT_MANAGER)
            ->only('findAllUserPermissionPaginated');

        $this->userService = $userService;
        $this->menuService = $menuService;
        $this->lookupService = $lookupService;
    }

    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->findAllPaginatedWithoutUser($request, auth()->id());
        return $this->showAll(new UserCollection($users, true));
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $userDTO = $request->toDTO();
        $user = $this->userService->create($userDTO);
        $this->menuService->createDefaultMenu($user->id, $userDTO->role->name);
        $token = $user->createToken('api-token')->accessToken;
        return $this->showOne(new UserResource($user, $token));
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findById($id);
        return $this->showOne(new UserResource($user));
    }

    /**
     * @throws CustomErrorException
     */
    public function changeStatus(int $id, ChangeStatusUserRequest $request): \Illuminate\Http\Response
    {
        $dto = $request->toDTO();
        $this->lookupService->validateLookup($dto->status_id, TypeLookup::STATUS_USER, 'Estatus no válido');
        $this->userService->changeStatus($id, $dto);
        return $this->noContentResponse();
    }

    public function showProfile(): JsonResponse
    {
        $user = $this->userService->findById(auth()->id());
        return $this->showOne(new UserResource($user));
    }

    /**
     * @throws CustomErrorException
     */
    public function storeDriver(StoreDriverRequest $request): JsonResponse
    {
        $userDTO = $request->toDTO();
        $user = $this->userService->storeDriver($userDTO);
        $this->menuService->createDefaultMenu($user->id, $userDTO->role->name);
        return $this->successResponse(['code' => Response::HTTP_OK], Response::HTTP_OK);
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, UpdateUserRequest $request): JsonResponse
    {
        $user = $this->userService->update($id, $request->toDTO());
        return $this->showOne(new UserResource($user));
    }

    /**
     * @throws CustomErrorException
     * @return StreamedResponse | \Illuminate\Http\Response
     */
    public function bulkStoreDriver(BulkStoreDriverRequest $request)
    {
        $result = $this->userService->bulkStoreDriver($request->toDTO());
        return ($result instanceof StreamedResponse) ? $result : $this->noContentResponse();
    }

    public function downUser(string $noEmployee): JsonResponse
    {
        $this->userService->downUser($noEmployee);
        return $this->successResponse(['code' => Response::HTTP_OK], Response::HTTP_OK);
    }

    /**
     * @throws CustomErrorException
     */
    public function updateCh(string $noEmployee, UpdateUserChRequest $request): JsonResponse
    {
        $this->userService->updateUser($noEmployee, $request->toDTO());
        return $this->successResponse(['code' => Response::HTTP_OK], Response::HTTP_OK);
    }

    public function findAllDepartmentManagers(): JsonResponse
    {
        $users = $this->userService->findAllDepartmentManagers();
        return $this->showAll(new UserCollection($users));
    }

    public function findAllUserPermissionPaginated(Request $request): JsonResponse
    {
        $users = $this->userService->findAllUserPermissionPaginated($request, auth()->user());
        return $this->showAll(new UserCollection($users, true));
    }
}
