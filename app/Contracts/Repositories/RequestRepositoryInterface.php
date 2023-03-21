<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Request create(array $data)
 * @method Request update(int $id, array $data)
 * @method Request findById(int $id, array $columns = ['*'])
 */
interface RequestRepositoryInterface extends BaseRepositoryInterface
{
    public function getRequestRoomScheduleByDate(Carbon $startDate, int $roomId): Collection;

    public function getProposalRequestRoomScheduleByDate(Carbon $startDate, int $roomId): Collection;

    public function roomsSetAsideByDay(Carbon $date): Collection;

    public function getAllApprovedCarDriverRoom(array $columns = ['*']): Collection;

    public function getExpired(): Collection;

    public function bulkStatusUpdate(array $ids, int $statusId): void;

    public function getApprovedRequestsTomorrow(): Collection;

    public function getTotalLast7Days(int $officeId): array;

    public function getTotalRequetsOfMonth(int $officeId): int;

    public function getTotalRequetsOfLastMonth(int $officeId): int;

    public function getRequestRoomAfterNowInWeekday(int $userId, int $roomId, int $weekday): Collection;

    public function getRequestRoomOfWeekdayByUser(int $userId, int $roomId): Collection;

    public function getTotalApplicantByStatus(int $userId, array $statusCodes = []): int;

    public function getTotalRecepcionistByStatus(int $officeId, array $statusCodes = []): int;

    public function getAllApprovedRecepcionistWithStartDateCondition(int $officeId, string $startDateOperator = '='): Collection;

    public function getAllApprovedApplicantWithStartDateCondition(int $userId, string $startDateOperator = '='): Collection;

    public function getTotalManagerRequestPackagesByStatus(int $departmentManagerId, array $statusCodes = []): int;
}