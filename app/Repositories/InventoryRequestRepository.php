<?php

namespace App\Repositories;

use App\Contracts\Repositories\InventoryRequestRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\InventoryRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class InventoryRequestRepository extends BaseRepository implements InventoryRequestRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|InventoryRequest
     */
    protected $entity;

    public function __construct(InventoryRequest $inventoryRequest)
    {
        $this->entity = $inventoryRequest;
    }

    /**
     * @throws \Throwable
     * @return void
     */
    public function deleteInventory(int $requestId, int $inventoryId)
    {
        $this->entity
            ->where('request_id', $requestId)
            ->where('inventory_id', $inventoryId)
            ->delete();
    }

    public function findByRequestIdAndInventoryId(int $requestId, int $inventoryId, array $fields = ['*']): InventoryRequest
    {
        return $this->entity
            ->where('request_id', $requestId)
            ->where('inventory_id', $inventoryId)
            ->firstOrFail($fields);
    }

    /**
     * @throws \Throwable
     * @return void
     */
    public function updateInventoryRequest(int $requestId, int $inventoryId, array $data)
    {
        $this->entity
            ->where('request_id', $requestId)
            ->where('inventory_id', $inventoryId)
            ->update($data);
    }

    public function getByRequestIdAndQuantity(int $requestId): Collection
    {
        return $this->entity
            ->where('request_id', $requestId)
            ->whereNotNull('quantity')
            ->get();
    }

    /**
     * @return void
     */
    public function deleteInventories(int $requestId)
    {
        $this->entity
            ->where('request_id', $requestId)
            ->delete();
    }
}