<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use Illuminate\Support\Collection;

interface MenuServiceInterface extends BaseServiceInterface
{
    public function createDefaultMenu(int $userId, string $role): void;

    public function getNavigationByUserId(int $userId): Collection;

    public function syncNavigation(int $userId, array $menuIds, array $submenuIds): void;

    public function hasPermissionToUrl(int $userId, string $pathUrl): bool;
}