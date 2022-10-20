<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Enums\Lookups\StatusRequestLookup;
use App\Models\Enums\Lookups\TypeRequestLookup;
use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class RequestRepository extends BaseRepository implements RequestRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Request
     */
    protected $entity;

    public function __construct(Request $request)
    {
        $this->entity = $request;
    }

    public function findById(int $id, array $columns = ['*']): Request
    {
        return $this->entity
            ->with(['type'])
            ->findOrFail($id, $columns);
    }

    public function isAvailableSchedule(Carbon $startDate, Carbon $endDate): bool
    {
        $requests = $this->entity
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->whereIn('lookups.code', [StatusRequestLookup::code(StatusRequestLookup::APPROVED),
                StatusRequestLookup::code(StatusRequestLookup::PROPOSAL)])
            ->count();
        return $requests === 0;
    }

    public function roomsSetAsideByDay(Carbon $date): Collection
    {
        return $this->entity
            ->with('status')
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->whereDate('start_date', $date)
            ->whereIn('lookups.code', [StatusRequestLookup::code(StatusRequestLookup::APPROVED),
                StatusRequestLookup::code(StatusRequestLookup::IN_REVIEW)])
            ->get();
    }

    public function getPreviouslyByCode(string $code, array $columns = ['*']): Collection
    {
        return $this->entity
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->whereDate('end_date', '<', now())
            ->where('lookups.code', $code)
            ->get($columns);
    }

    public function getExpired(array $columns = ['*']): Collection
    {
        return $this->entity
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->whereDate('end_date', '<', now())
            ->expired()
            ->get($columns);
    }

    /**
     * @return void
     */
    public function bulkStatusUpdate(array $ids, int $statusId)
    {
        $this->entity
            ->whereIn('id', $ids)
            ->update(['status_id' => $statusId]);
    }

    public function getApprovedRequestsTomorrow(): Collection
    {
        return $this->entity
            ->select(['requests.*'])
            ->join('lookups AS status', 'status.id', '=', 'requests.status_id')
            ->join('lookups AS type', 'type.id', '=', 'requests.type_id')
            // ->whereDate('start_date', now()->addDay())
            ->whereDate('start_date', '2022-10-27')
            ->where('status.code', StatusRequestLookup::code(StatusRequestLookup::APPROVED))
            ->whereIn('type.code', [TypeRequestLookup::code(TypeRequestLookup::ROOM),
                TypeRequestLookup::code(TypeRequestLookup::TRAVEL)])
            ->get();
    }
}