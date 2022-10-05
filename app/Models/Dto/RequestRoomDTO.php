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
    public $request_id;

    /**
     * @var RequestDTO
     */
    public $request;

    /**
     * @var int
     */
    public $room_id;

    /**
     * @var int
     */
    public $external_people;

    /**
     * @var int
     */
    public $level_id;

    /**
     * @var int
     */
    public $duration;

    /**
     * @var InventoryRequestDTO[]
     */
    public $inventoryRequest;

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