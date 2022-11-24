<?php

namespace App\Models\Dto;

use App\Models\Contracts\DataTransferObject;

class DriverCarDTO 
{
    
    use DataTransferObject;

    /**
     * @var int
     */
    public $car_id;

    /**
     * @var int
     */
    public $driver_id;

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
