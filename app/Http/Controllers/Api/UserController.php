<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\LookupServiceInterface;
use App\Contracts\Services\MenuServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\User\ChangeStatusUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    private $userService;
    private $menuService;
    private $lookupService;

    public function __construct(UserServiceInterface $userService,
                                MenuServiceInterface $menuService,
                                LookupServiceInterface $lookupService)
    {
        $this->middleware('role.permission:'.NameRole::ADMIN)
            ->only('index', 'show', 'changeStatus');
        $this->middleware('role.permission:'.NameRole::ADMIN.','.NameRole::RECEPCIONIST.','.
            NameRole::APPLICANT)->only('showProfile');

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

    public function changeStatus(int $id, ChangeStatusUserRequest $request): JsonResponse
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
}
