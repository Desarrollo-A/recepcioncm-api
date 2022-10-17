<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class RequestNotificationDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $notification_id;

    /**
     * @var int
     */
    public $request_id;

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