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
    private $id;

    /**
     * @var int
     */
    private $inventory_id;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var float
     */
    private $cost;

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
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
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
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @param float $cost
     */
    public function setCost(float $cost)
    {
        $this->cost = $cost;
    }
}