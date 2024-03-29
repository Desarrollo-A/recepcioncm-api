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
     * @var CancelRequestDTO
     */
    public $cancelRequest;

    /**
     * @var bool
     */
    public $add_google_calendar;

    /**
     * @var string
     */
    public $event_google_calendar_id;

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
     * @var ProposalRequestDTO[]
     */
    public $proposalRequest;

    /**
     * @var int
     */
    public $proposal_id;

    /**
     * @var RequestPhoneNumberDTO[]
     */
    public $requestPhoneNumber;

    /**
     * @var RequestEmailDTO[]
     */
    public $requestEmail;

    /**
     * @var ScoreDTO
     */
    public $score;

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