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
    private $request_id;

    /**
     * @var string
     */
    private $cancel_comment;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @throws CustomErrorException
     */
    public function __construct(array $data = [])
    {
        if (count($data) > 0) {
            $this->init($data);
        }
    }

    /**
     * @return int
     */
    public function getRequestId(): int
    {
        return $this->request_id;
    }

    /**
     * @param int $request_id
     */
    public function setRequestId(int $request_id)
    {
        $this->request_id = $request_id;
    }

    /**
     * @return string
     */
    public function getCancelComment(): string
    {
        return $this->cancel_comment;
    }

    /**
     * @param string $cancel_comment
     */
    public function setCancelComment(string $cancel_comment)
    {
        $this->cancel_comment = $cancel_comment;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;
    }
}