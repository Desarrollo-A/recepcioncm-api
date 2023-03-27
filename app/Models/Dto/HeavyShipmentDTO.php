<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;
use Carbon\Carbon;

class HeavyShipmentDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $package_id;

    /**
     * @var float
     */
    public $high;

    /**
     * @var float
     */
    public $long;

    /**
     * @var float
     */
    public $width;

    /**
     * @var float
     */
    public $weight;

    /**
     * @var string
     */
    public $description;

    /**
     * @var Carbon
     */
    public $created_at;

    /**
     * @var Carbon
     */
    public $updated_at;

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