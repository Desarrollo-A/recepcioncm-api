<?php

namespace App\Services;

use App\Contracts\Repositories\PerDiemRepositoryInterface;
use App\Contracts\Services\PerDiemServiceInterface;
use App\Core\BaseService;

class PerDiemService extends BaseService implements PerDiemServiceInterface
{
    protected $entityRepository;

    public function __construct(PerDiemRepositoryInterface $perDiemRepository)
    {
        $this->entityRepository = $perDiemRepository;
    }
}