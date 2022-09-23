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
    private $id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $title;

    /**
     * @var Carbon
     */
    private $start_date;

    /**
     * @var Carbon
     */
    private $end_date;

    /**
     * @var int
     */
    private $duration;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var string
     */
    private $cancel_comment;

    /**
     * @var bool
     */
    private $add_google_calendar;

    /**
     * @var int
     */
    private $people;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @var int
     */
    private $status_id;

    /**
     * @var LookupDTO
     */
    private $status;

    /**
     * @var ProposalRequestRoomDTO[]
     */
    private $proposalRequest;

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
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->start_date;
    }

    /**
     * @param Carbon $start_date
     */
    public function setStartDate(Carbon $start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     * @return Carbon
     */
    public function getEndDate(): Carbon
    {
        return $this->end_date;
    }

    /**
     * @param Carbon $end_date
     */
    public function setEndDate(Carbon $end_date)
    {
        $this->end_date = $end_date;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration(int $duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;
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
     * @return bool
     */
    public function isAddGoogleCalendar(): bool
    {
        return $this->add_google_calendar;
    }

    /**
     * @param bool $add_google_calendar
     */
    public function setAddGoogleCalendar(bool $add_google_calendar)
    {
        $this->add_google_calendar = $add_google_calendar;
    }

    /**
     * @return int
     */
    public function getPeople(): int
    {
        return $this->people;
    }

    /**
     * @param int $people
     */
    public function setPeople(int $people)
    {
        $this->people = $people;
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

    /**
     * @return int
     */
    public function getStatusId(): int
    {
        return $this->status_id;
    }

    /**
     * @param int $status_id
     */
    public function setStatusId(int $status_id)
    {
        $this->status_id = $status_id;
    }

    /**
     * @return LookupDTO
     */
    public function getStatus(): LookupDTO
    {
        return $this->status;
    }

    /**
     * @param LookupDTO $status
     */
    public function setStatus(LookupDTO $status)
    {
        $this->status = $status;
    }

    /**
     * @return ProposalRequestRoomDTO[]
     */
    public function getProposalRequest(): array
    {
        return $this->proposalRequest;
    }

    /**
     * @param ProposalRequestRoomDTO[] $proposalRequest
     */
    public function setProposalRequest(array $proposalRequest)
    {
        $this->proposalRequest = $proposalRequest;
    }
}