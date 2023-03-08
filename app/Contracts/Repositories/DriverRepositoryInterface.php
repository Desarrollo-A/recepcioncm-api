<?php
namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method User findById(int $id, array $columns = ['*'])
 */
interface DriverRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllPaginatedOffice(int $officeId, array $filters, int $limit, string $sort = null, array $columns = ['*']):
        LengthAwarePaginator;

    public function findAllByOfficeId(int $officeId): Collection;

    public function getAvailableDriversPackage(int $officeId, Carbon $date): Collection;

    public function getAvailableDriversRequest(int $officeId, Carbon $startDate, Carbon $endDate, int $people): Collection;

    public function getAvailableDriverProposal(int $officeId, Carbon $startDate, Carbon $endDate): Collection;
}
