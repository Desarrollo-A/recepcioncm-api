<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class DriverRequestScheduleDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $request_driver_id;

    /**
     * @var int
     */
    public $driver_schedule_id;

    /**
     * @var DriverScheduleDTO
     */
    public $driverSchedule;

    /**
     * @var int
     */
    public $car_schedule_id;

    /**
     * @var CarScheduleDTO
     */
    public $carSchedule;

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