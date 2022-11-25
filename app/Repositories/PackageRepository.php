<?php

namespace App\Repositories;

use App\Contracts\Repositories\PackageRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Package;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class PackageRepository extends BaseRepository implements PackageRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Package
     */
    protected $entity;

    public function __construct(Package $package)
    {
        $this->entity = $package;
    }

    public function findByRequestId(int $requestId): Package
    {
        return $this->entity
            ->where('request_id', $requestId)
            ->firstOrFail();
    }
}
