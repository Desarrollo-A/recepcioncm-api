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
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $trademark;

    /**
     * @var int
     */
    public $stock;

    /**
     * @var int
     */
    public $minimum_stock;

    /**
     * @var bool
     */
    public $status;

    /**
     * @var int
     */
    public $type_id;

    /**
     * @var int
     */
    public $unit_id;

    /**
     * @var int
     */
    public $office_id;

    /**
     * @var int
     */
    public $meeting;

    /**
     * @var string
     */
    public $image;

    /**
     * @var UploadedFile
     */
    public $image_file;

    /**
     * @var float
     */
    public $cost;

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