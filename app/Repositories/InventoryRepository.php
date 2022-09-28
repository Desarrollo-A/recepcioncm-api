<?php

namespace App\Repositories;

use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class InventoryRepository extends BaseRepository implements InventoryRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Inventory
     */
    protected $entity;

    public function __construct(Inventory $inventory)
    {
        $this->entity = $inventory;
    }

    public function findAllPaginatedOffice(User $user, array $filters, int $limit, string $sort = null,
                                           array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->with(['type', 'unit'])
            ->orWhereHas('type', function ($query) use ($filters) {
                $query->filter($filters);
            })
            ->orWhereHas('unit', function ($query) use ($filters) {
                $query->filter($filters);
            })
            ->filter($filters)
            ->filterOffice($user)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function findById(int $id, array $columns = ['*']): Inventory
    {
        return $this->entity
            ->with(['type', 'unit', 'office'])
            ->findOrFail($id, $columns);
    }

    public function findAllByType(int $typeId, int $officeId): Collection
    {
        return $this->entity
            ->where('type_id', $typeId)
            ->where('office_id', $officeId)
            ->get();
    }

    public function findAllSnacks(int $typeId, int $officeId): Collection
    {
        return $this->entity
            ->where('type_id', $typeId)
            ->where('office_id', $officeId)
            ->where('stock', '>', 0)
            ->orderBy('name', 'ASC')
            ->get();
    }
}