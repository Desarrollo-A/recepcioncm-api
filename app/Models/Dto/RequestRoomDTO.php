<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class RequestRoomDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    private $request_id;

    /**
     * @var RequestDTO
     */
    private $request;

    /**
     * @var int
     */
    private $room_id;

    /**
     * @var int
     */
    private $external_people;

    /**
     * @var int
     */
    private $level_id;

    /**
     * @var InventoryRequestDTO[]
     */
    private $inventoryRequest;

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
     * @return RequestDTO
     */
    public function getRequest(): RequestDTO
    {
        return $this->request;
    }

    /**
     * @param RequestDTO $request
     */
    public function setRequest(RequestDTO $request)
    {
        $this->request = $request;
    }

    /**
     * @return int
     */
    public function getRoomId(): int
    {
        return $this->room_id;
    }

    /**
     * @param int $room_id
     */
    public function setRoomId(int $room_id)
    {
        $this->room_id = $room_id;
    }

    /**
     * @return int
     */
    public function getExternalPeople(): int
    {
        return $this->external_people;
    }

    /**
     * @param int $external_people
     */
    public function setExternalPeople(int $external_people)
    {
        $this->external_people = $external_people;
    }

    /**
     * @return int
     */
    public function getLevelId(): int
    {
        return $this->level_id;
    }

    /**
     * @param int $level_id
     */
    public function setLevelId(int $level_id)
    {
        $this->level_id = $level_id;
    }

    /**
     * @return InventoryRequestDTO[]
     */
    public function getInventoryRequest(): array
    {
        return $this->inventoryRequest;
    }

    /**
     * @param InventoryRequestDTO[] $inventoryRequest
     */
    public function setInventoryRequest(array $inventoryRequest)
    {
        $this->inventoryRequest = $inventoryRequest;
    }
}