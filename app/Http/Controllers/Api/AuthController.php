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
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Pusher\Pusher;
use Pusher\PusherException;

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
        auth()->user()->token()->delete();
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

    /**
     * @throws AuthenticationException
     * @throws PusherException
     */
    public function pusherAuth(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            throw new AuthenticationException();
        }

        $pusher = new Pusher(config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'), config('broadcasting.connections.pusher.app_id'), [
                'cluster' => config('broadcasting.connections.pusher.options.cluster')
            ]);
        return $pusher->socket_auth($request->input('channel_name'), $request->input('socket_id'));
    }
}
