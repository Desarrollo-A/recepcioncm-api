<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;
use Illuminate\Http\UploadedFile;

class PerDiemDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $request_id;

    /**
     * @var float
     */
    public $gasoline;

    /**
     * @var float
     */
    public $tollbooths;

    /**
     * @var float
     */
    public $food;

    /**
     * @var string
     */
    public $bill_filename;

    /**
     * @var UploadedFile
     */
    public $bill_file;

    /**
     * @var float
     */
    public $spent;

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