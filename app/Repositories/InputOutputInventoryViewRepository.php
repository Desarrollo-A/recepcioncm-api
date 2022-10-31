<?php

namespace App\Repositories;

use App\Contracts\Repositories\InputOutputInventoryViewRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\InputOutputInventoryView;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class InputOutputInventoryViewRepository extends BaseRepository implements InputOutputInventoryViewRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|InputOutputInventoryView
     */
    protected $entity;

    public function __construct(InputOutputInventoryView $inputOutputInventoryView)
    {
        $this->entity = $inputOutputInventoryView;
    }

    public function findAllInventoriesPaginated(array $filters, int $limit, int $officeId, string $sort = null,
                                                array $columns = ['*']): LengthAwarePaginator
    {
        return $this->entity
            ->filter($filters)
            ->where('office_id', $officeId)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    public function getDataReport(array $filters, int $officeId): Collection
    {
        return $this->entity
            ->where('office_id', $officeId)
            ->filterReport($filters)
            ->orderByRaw('move_date DESC, type ASC')
            ->get();
    }
}