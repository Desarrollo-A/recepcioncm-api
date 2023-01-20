<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

interface DashboardServiceInterface
{
    /**
     * @param User|Authenticatable $user
     */
    public function infoCardRequests(User $user): Collection;

    /**
     * @param User|Authenticatable $user
     */
    public function getTotalLast7Days(User $user): array;

    /**
     * @param User|Authenticatable $user
     */
    public function getTotalRequetsOfMonth(User $user): int;

    /**
     * @param User|Authenticatable $user
     */
    public function getRequestPercentage(User $user): int;
}