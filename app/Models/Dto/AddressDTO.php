<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class AddressDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $num_ext;

    /**
     * @var string
     */
    public $num_int;

    /**
     * @var string
     */
    public $suburb;

    /**
     * @var string
     */
    public $postal_code;

    /**
     * @var string
     */
    public $state;

    /**
     * @var int
     */
    public $country_id;

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