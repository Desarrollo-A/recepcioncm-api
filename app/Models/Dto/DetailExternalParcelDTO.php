<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class DetailExternalParcelDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $package_id;

    /**
     * @var string
     */
    public $company_name;

    /**
     * @var string
     */
    public $tracking_code;

    /**
     * @var string
     */
    public $url_tracking;

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