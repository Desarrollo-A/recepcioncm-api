<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class CarDTO
{
    use DataTransferObject;

    /**
     * @var $id
     */
    private $id;

    /**
     * @var string
     */
    private $business_name;

    /**
     * @var string
     */
    private $trademark;

    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $color;

    /**
     * @var string
     */
    private $license_plate;

    /**
     * @var string
     */
    private $serie;

    /**
     * @var string
     */
    private $circulation_card;

    /**
     * @var int
     */
    private $office_id;

    /**
     * @var int
     */
    private $status_id;

    /**
     * @var int
     */
    private $people;

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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getBusinessName(): string
    {
        return $this->business_name;
    }

    /**
     * @param string $business_name
     */
    public function setBusinessName(string $business_name)
    {
        $this->business_name = $business_name;
    }

    /**
     * @return string
     */
    public function getTrademark(): string
    {
        return $this->trademark;
    }

    /**
     * @param string $trademark
     */
    public function setTrademark(string $trademark)
    {
        $this->trademark = $trademark;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel(string $model)
    {
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color)
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getLicensePlate(): string
    {
        return $this->license_plate;
    }

    /**
     * @param string $license_plate
     */
    public function setLicensePlate(string $license_plate)
    {
        $this->license_plate = $license_plate;
    }

    /**
     * @return string
     */
    public function getSerie(): string
    {
        return $this->serie;
    }

    /**
     * @param string $serie
     */
    public function setSerie(string $serie)
    {
        $this->serie = $serie;
    }

    /**
     * @return string
     */
    public function getCirculationCard(): string
    {
        return $this->circulation_card;
    }

    /**
     * @param string $circulation_card
     */
    public function setCirculationCard(string $circulation_card)
    {
        $this->circulation_card = $circulation_card;
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
}