<?php

namespace App\Services;

use App\Contracts\Repositories\MenuRepositoryInterface;
use App\Contracts\Repositories\SubmenuRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use App\Mail\Auth\RestorePasswordMail;
use App\Models\Dto\UserDTO;
use App\Models\Enums\Lookups\StatusUserLookup;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthService extends BaseService implements AuthServiceInterface
{
    protected $userRepository;
    protected $menuRepository;
    protected $submenuRepository;

    public function __construct(UserRepositoryInterface $userRepository,
                                MenuRepositoryInterface $menuRepository,
                                SubmenuRepositoryInterface $submenuRepository)
    {
        $this->userRepository = $userRepository;
        $this->menuRepository = $menuRepository;
        $this->submenuRepository = $submenuRepository;
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function changePassword(UserDTO $userDTO)
    {
        $user = $this->userRepository->findById($userDTO->id);
        if (!Hash::check($userDTO->currentPassword, $user->password)) {
            throw new CustomErrorException(Message::PASSWORD_INVALID, 400);
        }
        $this->userRepository->update($userDTO->id, $userDTO->toArray(['password']));
    }

    public function getNavigationMenu(int $userId): Collection
    {
        $menus = $this->menuRepository->findByUserId($userId);
        $submenus = $this->submenuRepository->findByUserId($userId);

        return $menus->map(function ($menu) use ($submenus) {
            $submenusArr = $submenus->filter(function ($submenu) use ($menu) {
                return $submenu->menu_id === $menu->id;
            })
                ->map(function ($submenu) use ($menu) {
                    $submenu['path_route'] = $menu['path_route'].$submenu['path_route'];
                    return $submenu;
                })
                ->values();

            return collect($menu)->put('submenu', $submenusArr);
        });
    }

    public function getUser(int $id): User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * @throws CustomErrorException
     */
    public function login(string $noEmployee, string $password): Collection
    {
        $user = $this->checkAccount($noEmployee, $password);
        $token = $user->createToken('api-token')->accessToken;
        return collect($user)->put('token', $token);
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    public function restorePassword(string $email)
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user->status->code === StatusUserLookup::code(StatusUserLookup::INACTIVE)) {
            throw new CustomErrorException(Message::USER_INACTIVE, Response::HTTP_BAD_REQUEST);
        }

        if ($user->status->code === StatusUserLookup::code(StatusUserLookup::BLOCKED)) {
            throw new CustomErrorException(Message::USER_BLOCKED, Response::HTTP_BAD_REQUEST);
        }

        $newPassword = Str::random(User::RESTORE_PASSWORD_LENGTH);
        $userDTO = new UserDTO(['password' => bcrypt($newPassword)]);

        $user = $this->userRepository->update($user->id, $userDTO->toArray(['password']));
        Mail::to($user)->send(new RestorePasswordMail($user, $newPassword));
    }

    /**
     * @throws CustomErrorException
     */
    private function checkAccount(string $noEmployee, string $password): User
    {
        try {
            $user = $this->userRepository->findByNoEmployee($noEmployee);
        } catch (ModelNotFoundException $ex) {
            throw new CustomErrorException(Message::CREDENTIALS_INVALID, Response::HTTP_BAD_REQUEST);
        }

        if ($user->status->code === StatusUserLookup::code(StatusUserLookup::INACTIVE)) {
            throw new CustomErrorException(Message::USER_INACTIVE, Response::HTTP_BAD_REQUEST);
        }

        if ($user->status->code === StatusUserLookup::code(StatusUserLookup::BLOCKED)) {
            throw new CustomErrorException(Message::USER_BLOCKED, Response::HTTP_BAD_REQUEST);
        }

        if (!Hash::check($password, $user->password)) {
            throw new CustomErrorException(Message::CREDENTIALS_INVALID, Response::HTTP_BAD_REQUEST);
        }

        return $user;
    }
}