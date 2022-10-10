<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;
use Carbon\Carbon;

class RequestDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $title;

    /**
     * @var Carbon
     */
    public $start_date;

    /**
     * @var Carbon
     */
    public $end_date;

    /**
     * @var int
     */
    public $type_id;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var string
     */
    public $cancel_comment;

    /**
     * @var bool
     */
    public $add_google_calendar;

    /**
     * @var int
     */
    public $people;

    /**
     * @var int
     */
    public $user_id;

    /**
     * @var int
     */
    public $status_id;

    /**
     * @var LookupDTO
     */
    public $status;

    /**
     * @var ProposalRequestRoomDTO[]
     */
    public $proposalRequest;

    /**
     * @var RequestPhoneNumberDTO[]
     */
    public $requestPhoneNumber;

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