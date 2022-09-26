<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\UserDTO;
use App\Models\User;
use Illuminate\Support\Collection;

interface AuthServiceInterface extends BaseServiceInterface
{
    /**
     * @return void
     */
    public function changePassword(UserDTO $userDTO);

    public function getNavigationMenu(int $userId): Collection;

    public function getUser(int $id): User;

    public function login(string $noEmployee, string $password): Collection;

    /**
     * @return void
     */
    public function restorePassword(string $email);
}