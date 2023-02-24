<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class OfficeDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $address_id;

    /**
     * @var AddressDTO
     */
    public $address;

    /**
     * @var int
     */
    public $state_id;

    /**
     * @var bool
     */
    public $status;

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