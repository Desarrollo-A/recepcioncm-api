<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class RoomDTO
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
    private $name;

    /**
     * @var int
     */
    private $office_id;

    /**
     * @var int
     */
    private $no_people;

    /**
     * @var int
     */
    private $recepcionist_id;

    /**
     * @var int
     */
    private $status_id;

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getOfficeId(): int
    {
        return $this->office_id;
    }

    /**
     * @param int $office_id
     */
    public function setOfficeId(int $office_id)
    {
        $this->office_id = $office_id;
    }

    /**
     * @return int
     */
    public function getNoPeople(): int
    {
        return $this->no_people;
    }

    /**
     * @param int $no_people
     */
    public function setNoPeople(int $no_people)
    {
        $this->no_people = $no_people;
    }

    /**
     * @return int
     */
    public function getRecepcionistId(): int
    {
        return $this->recepcionist_id;
    }

    /**
     * @param int $recepcionist_id
     */
    public function setRecepcionistId(int $recepcionist_id)
    {
        $this->recepcionist_id = $recepcionist_id;
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
}