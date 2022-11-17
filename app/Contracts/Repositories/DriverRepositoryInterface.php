<?php
namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface DriverRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllPaginatedOffice(int $OfficeId, array $filters, int $limit, string $sort = null, array $columns = ['*']):
    LengthAwarePaginator;
}
