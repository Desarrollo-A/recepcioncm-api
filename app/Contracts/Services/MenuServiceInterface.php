<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;

interface MenuServiceInterface extends BaseServiceInterface
{
    /**
     * @return void
     */
    public function createDefaultMenu(int $userId, string $role);
}