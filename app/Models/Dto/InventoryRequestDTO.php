<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class InventoryRequestDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $request_id;

    /**
     * @var int
     */
    public $inventory_id;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var bool
     */
    public $applied;

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