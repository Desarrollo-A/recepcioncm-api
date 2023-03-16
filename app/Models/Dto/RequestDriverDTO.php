<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;
use Illuminate\Http\UploadedFile;

class RequestDriverDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $pickup_address_id;

    /**
     * @var AddressDTO
     */
    public $pickupAddress;

    /**
     * @var int
     */
    public $arrival_address_id;

    /**
     * @var AddressDTO
     */
    public $arrivalAddress;

    /**
     * @var int
     */
    public $office_id;

    /**
     * @var int
     */
    public $request_id;

    /**
     * @var RequestDTO
     */
    public $request;

    /**
     * @var DriverRequestScheduleDTO
     */
    public $driverRequestSchedule;

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