<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\InventoryDTO;
use App\Models\Inventory;
use App\Models\InventoryRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method Inventory findById(int $id)
 */
interface InventoryServiceInterface extends BaseServiceInterface
{
    public function create(InventoryDTO $dto): Inventory;

    public function update(int $id, InventoryDTO $dto): Inventory;

    public function findAllPaginatedOffice(Request $request, User $user, array $columns = ['*']): LengthAwarePaginator;

    /**
     * @return void
     */
    public function updateStock(int $id, InventoryDTO $dto);

    /**
     * @return void
     */
    public function updateImage(int $id, InventoryDTO $dto);

    /**
     * @return void
     */
    public function updateStockAfterApprove(InventoryRequest $inventoryRequest);

    public function findAllCoffee(int $officeId): Collection;

    /**
     * @return void
     */
    public function restoreStockAfterInventoryRequestDeleted(InventoryRequest $inventoryRequest);

    /**
     * @return void
     */
    public function restoreStockAfterInventoriesRequestDeleted(Collection $inventoriesRequest);

    /**
     * @return void
     */
    public function deleteImage(int $id);
}