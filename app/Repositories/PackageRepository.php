<?php

namespace App\Repositories;

use App\Contracts\Repositories\PackageRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Package;

class PackageRepository extends BaseRepository implements PackageRepositoryInterface
{
    protected $entity;

    public function __construct(Package $package)
    {
        $this->entity = $package;
    }
}