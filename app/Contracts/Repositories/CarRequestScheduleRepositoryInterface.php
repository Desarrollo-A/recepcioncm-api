<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;

interface CarRequestScheduleRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteByRequestCarId(int $requestCarId): void;
}