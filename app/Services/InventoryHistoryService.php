<?php

namespace App\Services;

use App\Contracts\Repositories\InventoryHistoryRepositoryInterface;
use App\Contracts\Services\InventoryHistoryServiceInterface;
use App\Core\BaseService;

class InventoryHistoryService extends BaseService implements InventoryHistoryServiceInterface
{
    protected $entityRepository;

    public function __construct(InventoryHistoryRepositoryInterface $inventoryHistoryRepository)
    {
        $this->entityRepository = $inventoryHistoryRepository;
    }
}