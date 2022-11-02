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

    public function getSnacksUncountable(): Collection
    {
        return $this->entity
            ->selectRaw('inventory_id, COUNT(inventory_id) AS total, meeting')
            ->with(['inventory'])
            ->join('requests', 'requests.id', '=', 'inventory_request.request_id')
            ->join('inventories', 'inventories.id', '=', 'inventory_request.inventory_id')
            ->whereNotNull('meeting')
            ->where('applied', false)
            ->whereDate('start_date','<', now())
            ->groupBy(['inventory_id', 'meeting'])
            ->havingRaw('COUNT(inventory_id) >= meeting')
            ->get();
    }

    public function updateSnackUncountableApplied(int $inventoryId, int $limit)
    {
        $this->entity
            ->whereIn('request_id', function ($query) use ($inventoryId, $limit) {
                $query
                    ->selectRaw("TOP $limit request_id")
                    ->from('inventory_request')
                    ->where('inventory_id', $inventoryId)
                    ->orderBy('created_at', 'ASC');
            })
            ->where('inventory_id', $inventoryId)
            ->update(['applied' => true]);
    }

    public function getSnackCountableRequestNotApplied(): Collection
    {
        return $this->entity
            ->join('requests', 'requests.id', '=', 'inventory_request.request_id')
            ->whereDate('start_date','<', now())
            ->where('applied', false)
            ->whereNotNull('quantity')
            ->get(['inventory_request.*']);
    }

    public function updateSnackCountableRequestToApplied()
    {
        $this->entity
            ->join('requests', 'requests.id', '=', 'inventory_request.request_id')
            ->whereDate('start_date','<', now())
            ->where('applied', false)
            ->whereNotNull('quantity')
            ->update(['applied' => true]);
    }

    public function bulkInsert(array $data): bool
    {
        return $this->entity
            ->insert($data);
    }
}