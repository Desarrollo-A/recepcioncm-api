<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\PerDiemServiceInterface;
use App\Core\BaseApiController;

class PerDiemController extends BaseApiController
{
    private $perDiemService;

    public function __construct(PerDiemServiceInterface $perDiemService)
    {
        $this->perDiemService = $perDiemService;
    }
}
