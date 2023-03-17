<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\ProposalRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method ProposalRequest findById(int $id, array $columns = ['*'])
 */
interface ProposalRequestRepositoryInterface extends BaseRepositoryInterface
{
    public function roomsSetAsideByDay(Carbon $date): Collection;

    public function findOneByRequestId(int $requestId): ProposalRequest;
}