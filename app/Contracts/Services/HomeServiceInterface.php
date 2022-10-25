<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Support\Collection;

interface HomeServiceInterface
{
    public function infoCardRequests(User $user): Collection;

    public function getTotalLast7Days(User $user): array;

    public function getTotalRequetsOfMonth(User $user): int;

    public function getRequestPercentage(User $user): int;
}