<?php
namespace App\Repositories;

use App\Contracts\Repositories\DriverRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverRepository extends BaseRepository implements DriverRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|
     */
    protected $entity;

    public function __construct(Driver $dri)
    {
        $this->entity = $dri;
    }

    public function findAllPaginatedOffice(int $OfficeId, array $filters, int $limit, string $sort = null, array $columns = ['*']):
        LengthAwarePaginator
    {
        return $this->entity
            ->with('status', 'office')
            ->filter($filters)
            ->where('office_id', $OfficeId)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function findById(int $id, array $columns = ['*'])
    {
        return $this->entity
            ->with('cars')
            ->findOrFail($id, $columns);
    }

}

