<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class ActionRequestNotificationDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $request_notification_id;

    /**
     * @var int
     */
    public $is_answered;

    /**
     * @var int
     */
    public $type_id;

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