<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class InventoryHistoryDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $inventory_id;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var float
     */
    public $cost;

    /**
     * @throws CustomErrorException
     */
    public function __construct(array $data = [])
    {
        if (count($data) > 0) {
            $this->init($data);
        }
    }
}