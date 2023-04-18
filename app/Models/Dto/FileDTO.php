<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileDTO
{
    use DataTransferObject;

    /**
     * @var string
     */
    public $filename;

    /**
     * @var UploadedFile
     */
    public $file;

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