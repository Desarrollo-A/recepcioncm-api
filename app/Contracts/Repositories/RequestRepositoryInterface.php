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
    public function isAvailableSchedule(Carbon $startDate, Carbon $endDate): bool;

    public function roomsSetAsideByDay(Carbon $date): Collection;

    public function getPreviouslyByCode(string $code, array $columns = ['*']): Collection;

    public function getExpired(array $columns = ['*']): Collection;

    /**
     * @return void
     */
    public function bulkStatusUpdate(array $ids, int $statusId);

    public function getApprovedRequestsTomorrow(): Collection;

    public function getTotalLast7Days(int $officeId): array;

    public function getTotalRequetsOfMonth(int $officeId): int;

    public function getTotalRequetsOfLastMonth(int $officeId): int;

    public function getTotalRequestRoomOfWeekday(int $userId, int $weekday): int;

    public function getRequestRoomAfterNowInWeekday(int $userId, int $weekday): Collection;

    public function getTotalApplicantByStatus(int $userId, array $statusCodes = []): int;

    public function getTotalRecepcionistByStatus(int $officeId, array $statusCodes = []): int;

    public function getAllApprovedRecepcionistWithStartDateCondition(int $officeId, string $startDateOperator = '='): Collection;

    public function getAllApprovedApplicantWithStartDateCondition(int $userId, string $startDateOperator = '='): Collection;
}