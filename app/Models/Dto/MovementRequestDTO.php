<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class MovementRequestDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $request_id;

    /**
     * @var int
     */
    public $user_id;

    /**
     * @var string
     */
    public $description;

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