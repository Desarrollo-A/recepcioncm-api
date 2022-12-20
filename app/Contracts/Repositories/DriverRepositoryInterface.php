<?php
namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface DriverRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllPaginatedOffice(int $OfficeId, array $filters, int $limit, string $sort = null, array $columns = ['*']):
        LengthAwarePaginator;

    public function findAllByOfficeId(int $officeId): Collection;

    public function getAvailableDriversPackage(int $officeId, Carbon $date): Collection;

    public function getAvailableDriversRequest(int $officeId, Carbon $startDate, Carbon $endDate): Collection;
}
