<?php

namespace App\Repositories;

use App\Contracts\Repositories\DeliveredPackageRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\DeliveredPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class DeliveredPackageRepository extends BaseRepository implements DeliveredPackageRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|DeliveredPackage
     */
    protected $entity;

    public function __construct(DeliveredPackage $deliveredPackage)
    {
        $this->entity = $deliveredPackage;
    }
}