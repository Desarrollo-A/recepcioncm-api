<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;
use Illuminate\Http\UploadedFile;

class DeliveredPackageDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $package_id;

    /**
     * @var string
     */
    public $name_receive;

    /**
     * @var string
     */
    public $signature;

    /**
     * @var UploadedFile
     */
    public $signature_file;

    /**
     * @var string
     */
    public $observations;

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