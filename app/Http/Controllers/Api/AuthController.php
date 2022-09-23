<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\AuthServiceInterface;
use App\Core\BaseApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseApiController
{
    private $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $userDTO = $request->toDTO();
        $data = $this->authService->login($userDTO->getNoEmployee(), $userDTO->getPassword());
        return $this->successResponse(new LoginResource($data), 200);
    }

    public function getUser(): JsonResponse
    {
        $user = $this->authService->getUser(auth()->id());
        return $this->showOne(new UserResource($user));
    }

    public function logout(): JsonResponse
    {
        auth()->user()->token()->revoke();
        return $this->noContentResponse();
    }
}
