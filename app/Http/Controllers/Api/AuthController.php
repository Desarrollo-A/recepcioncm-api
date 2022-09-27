<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\AuthServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RestorePasswordRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Menu\MenuResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseApiController
{
    private $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @throws CustomErrorException
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $userDTO = $request->toDTO();
        $this->authService->changePassword($userDTO);
        return $this->noContentResponse();
    }

    public function getNavigationMenu(): JsonResponse
    {
        $menu = $this->authService->getNavigationMenu(auth()->id());
        return $this->showAll(MenuResource::collection($menu));
    }

    public function getUser(): JsonResponse
    {
        $user = $this->authService->getUser(auth()->id());
        return $this->showOne(new UserResource($user));
    }

    /**
     * @throws CustomErrorException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $userDTO = $request->toDTO();
        $data = $this->authService->login($userDTO->no_employee, $userDTO->password);
        return $this->successResponse(new LoginResource($data), 200);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->token()->revoke();
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function restorePassword(RestorePasswordRequest $request): JsonResponse
    {
        $userDTO = $request->toDTO();
        $this->authService->restorePassword($userDTO->email);
        return $this->noContentResponse();
    }
}
