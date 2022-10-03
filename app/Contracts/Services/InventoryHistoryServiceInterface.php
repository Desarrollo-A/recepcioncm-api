<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Inventory;

interface InventoryHistoryServiceInterface extends BaseServiceInterface
{
    /**
     * @return void
     */
    public function store(Inventory $inventory);
}