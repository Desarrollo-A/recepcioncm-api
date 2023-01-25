<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProposalRequestRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\InventoryRequest;
use App\Models\ProposalRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ProposalRequestRepository extends BaseRepository implements ProposalRequestRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|ProposalRequest
     */
    protected $entity;

    public function __construct(ProposalRequest $proposalRequest)
    {
        $this->entity = $proposalRequest;
    }

    public function roomsSetAsideByDay(Carbon $date): Collection
    {
        return $this->entity
            ->select(['proposal_requests.start_date', 'proposal_requests.end_date'])
            ->join('requests', 'proposal_requests.request_id', '=', 'requests.id')
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->where('lookups.code', StatusRoomRequestLookup::code(StatusRoomRequestLookup::PROPOSAL))
            ->whereDate('proposal_requests.start_date', $date)
            ->get();
    }

    public function deleteByRequestId(int $requestId): void
    {
        $this->entity->where('request_id', $requestId)->delete();
    }

    public function deleteInRequestIds(array $ids): void
    {
        $this->entity->whereIn('request_id', $ids)->delete();
    }

    public function findOneByRequestId(int $requestId): ProposalRequest
    {
        return $this->entity
            ->where('request_id', $requestId)
            ->firstOrFail();
    }
}