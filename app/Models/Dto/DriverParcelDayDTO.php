<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class DriverParcelDayDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $driver_id;

    /**
     * @var int
     */
    public $day_id;

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