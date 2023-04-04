<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class LookupDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $type;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $value;

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