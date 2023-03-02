<?php

namespace App\Models\Dto;

use App\Exceptions\CustomErrorException;
use App\Models\Contracts\DataTransferObject;

class ProposalPackageDTO
{
    use DataTransferObject;

    /**
     * @var int
     */
    public $package_id;

    /**
     * @var boolean
     */
    public $is_driver_selected;

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