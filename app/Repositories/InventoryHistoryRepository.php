<?php

namespace App\Repositories;

use App\Contracts\Repositories\InventoryHistoryRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\InventoryHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class InventoryHistoryRepository extends BaseRepository implements InventoryHistoryRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|InventoryHistory
     */
    protected $entity;

    public function __construct(InventoryHistory $inventoryHistory)
    {
        $this->entity = $inventoryHistory;
    }
}