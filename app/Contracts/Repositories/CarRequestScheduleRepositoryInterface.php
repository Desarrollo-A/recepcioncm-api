<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\CarRequestSchedule;

/**
 * @method CarRequestSchedule create(array $data)
 */
interface CarRequestScheduleRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteByRequestCarId(int $requestCarId): void;
}