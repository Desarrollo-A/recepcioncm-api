<?php

namespace App\Services;

use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Services\RoleServiceInterface;
use App\Core\BaseService;

class RoleService extends BaseService implements RoleServiceInterface
{
    protected $entityRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->entityRepository = $roleRepository;
    }
}