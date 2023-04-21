<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use Illuminate\Support\Collection;

interface MenuServiceInterface extends BaseServiceInterface
{
    public function createDefaultMenu(int $userId, string $role): void;

    public function getNavigationByUserId(int $userId): Collection;
}