<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;
use Carbon\Carbon;

class ProposalRequestRoomDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    private $request_id;

    /**
     * @var Carbon
     */
    private $start_date;

    /**
     * @var Carbon
     */
    private $end_date;

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
}