<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;
use Carbon\Carbon;

class DriverScheduleDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $driver_id;

    /**
     * @var Carbon
     */
    public $start_date;

    /**
     * @var Carbon
     */
    public $end_date;

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