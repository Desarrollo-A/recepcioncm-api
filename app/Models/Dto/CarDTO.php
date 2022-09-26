<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class CarDTO
{
    use DataTransferObject;

    /**
     * @var $id
     */
    public $id;

    /**
     * @var string
     */
    public $business_name;

    /**
     * @var string
     */
    public $trademark;

    /**
     * @var string
     */
    public $model;

    /**
     * @var string
     */
    public $color;

    /**
     * @var string
     */
    public $license_plate;

    /**
     * @var string
     */
    public $serie;

    /**
     * @var string
     */
    public $circulation_card;

    /**
     * @var int
     */
    public $office_id;

    /**
     * @var int
     */
    public $status_id;

    /**
     * @var int
     */
    public $people;

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