<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class NotificationDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $message;

    /**
     * @var bool
     */
    public $is_read;

    /**
     * @var int
     */
    public $user_id;

    /**
     * @var int
     */
    public $request_id;

    /**
     * @var int
     */
    public $type_id;

    /**
     * @var int
     */
    public $color_id;

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