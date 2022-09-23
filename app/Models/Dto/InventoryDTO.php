<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;
use Illuminate\Http\UploadedFile;

class InventoryDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $trademark;

    /**
     * @var int
     */
    private $stock;

    /**
     * @var int
     */
    private $minimum_stock;

    /**
     * @var bool
     */
    private $status;

    /**
     * @var int
     */
    private $type_id;

    /**
     * @var int
     */
    private $unit_id;

    /**
     * @var int
     */
    private $officeId;

    /**
     * @var int
     */
    private $meeting;

    /**
     * @var string
     */
    private $image;

    /**
     * @var UploadedFile
     */
    private $image_file;

    /**
     * @var float
     */
    private $cost;

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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
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
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     */
    public function setStock(int $stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return int
     */
    public function getMinimumStock(): int
    {
        return $this->minimum_stock;
    }

    /**
     * @param int $minimum_stock
     */
    public function setMinimumStock(int $minimum_stock)
    {
        $this->minimum_stock = $minimum_stock;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getTypeId(): int
    {
        return $this->type_id;
    }

    /**
     * @param int $type_id
     */
    public function setTypeId(int $type_id)
    {
        $this->type_id = $type_id;
    }

    /**
     * @return int
     */
    public function getUnitId(): int
    {
        return $this->unit_id;
    }

    /**
     * @param int $unit_id
     */
    public function setUnitId(int $unit_id)
    {
        $this->unit_id = $unit_id;
    }

    /**
     * @return int
     */
    public function getOfficeId(): int
    {
        return $this->officeId;
    }

    /**
     * @param int $officeId
     */
    public function setOfficeId(int $officeId)
    {
        $this->officeId = $officeId;
    }

    /**
     * @return int
     */
    public function getMeeting(): int
    {
        return $this->meeting;
    }

    /**
     * @param int $meeting
     */
    public function setMeeting(int $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image)
    {
        $this->image = $image;
    }

    /**
     * @return UploadedFile
     */
    public function getImageFile(): UploadedFile
    {
        return $this->image_file;
    }

    /**
     * @param UploadedFile $imageFile
     */
    public function setImageFile(UploadedFile $imageFile)
    {
        $this->image_file = $imageFile;
    }

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @param float $cost
     */
    public function setCost(float $cost)
    {
        $this->cost = $cost;
    }
}