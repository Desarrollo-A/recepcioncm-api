<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Enums\Lookups\StatusRequestLookup;
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
}