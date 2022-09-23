<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\User;
use Illuminate\Support\Collection;

interface AuthServiceInterface extends BaseServiceInterface
{
    public function login(string $noEmployee, string $password): Collection;

    public function getUser(int $id): User;
}