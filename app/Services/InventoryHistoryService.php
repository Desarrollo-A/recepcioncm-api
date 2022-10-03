<?php

namespace App\Services;

use App\Contracts\Repositories\InventoryHistoryRepositoryInterface;
use App\Contracts\Services\InventoryHistoryServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Models\Dto\InventoryHistoryDTO;
use App\Models\Inventory;

class InventoryHistoryService extends BaseService implements InventoryHistoryServiceInterface
{
    protected $entityRepository;

    public function __construct(InventoryHistoryRepositoryInterface $inventoryHistoryRepository)
    {
        $this->entityRepository = $inventoryHistoryRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(Inventory $inventory)
    {
        $newStock = $inventory->stock;
        $oldStock = $inventory->getOriginal('stock');
        $dto = new InventoryHistoryDTO([
            'inventory_id' => $inventory->id,
            'quantity' => $newStock - $oldStock
        ]);
        $this->entityRepository->create($dto->toArray(['inventory_id', 'quantity']));
    }
}