<?php

namespace App\Observers;

use App\Contracts\Services\InventoryHistoryServiceInterface;
use App\Models\Inventory;

class InventoryObserver
{
    private $inventoryHistory;

    public function __construct(InventoryHistoryServiceInterface $inventoryHistory)
    {
        $this->inventoryHistory = $inventoryHistory;
    }

    /**
     * @return void
     */
    public function updated(Inventory $inventory)
    {
        if ($inventory->isDirty('stock')) {
            $this->inventoryHistory->store($inventory);
        }
    }
}