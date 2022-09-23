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
    private $request_id;

    /**
     * @var int
     */
    private $inventory_id;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var bool
     */
    private $applied;

    /**
     * @throws CustomErrorException
     */
    public function __construct(array $data = [])
    {
        if (count($data) > 0) {
            $this->init($data);
        }
    }

    /**
     * @return int
     */
    public function getRequestId(): int
    {
        return $this->request_id;
    }

    /**
     * @param int $request_id
     */
    public function setRequestId(int $request_id)
    {
        $this->request_id = $request_id;
    }

    /**
     * @return int
     */
    public function getInventoryId(): int
    {
        return $this->inventory_id;
    }

    /**
     * @param int $inventory_id
     */
    public function setInventoryId(int $inventory_id)
    {
        $this->inventory_id = $inventory_id;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return bool
     */
    public function isApplied(): bool
    {
        return $this->applied;
    }

    /**
     * @param bool $applied
     */
    public function setApplied(bool $applied)
    {
        $this->applied = $applied;
    }
}