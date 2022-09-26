<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class CancelRequestDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $request_id;

    /**
     * @var string
     */
    public $cancel_comment;

    /**
     * @var int
     */
    public $user_id;

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