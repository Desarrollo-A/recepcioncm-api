<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;
use Illuminate\Http\UploadedFile;

class RequestCarDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $id;

    /**
     * @var UploadedFile
     */
    public $authorization_file;

    /**
     * @var string
     */
    public $authorization_filename;

    /**
     * @var UploadedFile
     */
    public $responsive_file;

    /**
     * @var string
     */
    public $responsive_filename;

    /**
     * @var int
     */
    public $office_id;

    /**
     * @var int
     */
    public $request_id;

    /**
     * @var RequestDTO
     */
    public $request;

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