<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\DriverRequestSchedule;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method DriverRequestSchedule create(array $data)
 */
interface DriverRequestScheduleRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteByRequestDriverId(int $requestDriverId): void;

    public function getBusyDaysForProposalCalendar(): Collection;
}