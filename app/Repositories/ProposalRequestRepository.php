<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProposalRequestRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Enums\Lookups\StatusRequestLookup;
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
            ->join('requests', 'proposal_requests.request_id', '=', 'requests.id')
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->where('lookups.code', StatusRequestLookup::code(StatusRequestLookup::PROPOSAL))
            ->whereDate('proposal_requests.start_date', $date)
            ->get();
    }

    /**
     * @return void
     */
    public function deleteByRequestId(int $requestId)
    {
        $this->entity->where('request_id', $requestId)->delete();
    }
}