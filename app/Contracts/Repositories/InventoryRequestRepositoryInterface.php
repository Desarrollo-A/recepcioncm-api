<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\InventoryRequest;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method InventoryRequest create(array $data)
 */
interface InventoryRequestRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return void
     */
    public function deleteInventory(int $requestId, int $inventoryId);

    public function findByRequestIdAndInventoryId(int $requestId, int $inventoryId, array $fields = ['*']): InventoryRequest;

    /**
     * @return void
     */
    public function updateInventoryRequest(int $requestId, int $inventoryId, array $data);

    public function getByRequestIdAndQuantity(int $requestId): Collection;

    /**
     * @return void
     */
    public function deleteInventories(int $requestId);

    public function getSnacksUncountable(): Collection;

    /**
     * @return void
     */
    public function updateSnackUncountableApplied(int $inventoryId, int $limit);

    public function getSnackCountableRequestNotApplied(): Collection;

    /**
     * @return void
     */
    public function updateSnackCountableRequestToApplied();
}