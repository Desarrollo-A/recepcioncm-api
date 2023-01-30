<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\CarRequestSchedule;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method CarRequestSchedule create(array $data)
 */
interface CarRequestScheduleRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteByRequestCarId(int $requestCarId): void;

    public function getBusyDaysForProposalCalendar(): Collection;

    public function bulkDeleteByRequestCarId(array $requestCarIds): bool;
}