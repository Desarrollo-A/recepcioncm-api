<?php

namespace App\Repositories;

use App\Contracts\Repositories\DetailExternalParcelRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\DetailExternalParcel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class DetailExternalParcelRepository extends BaseRepository implements DetailExternalParcelRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|DetailExternalParcel
     */
    protected $entity;

    public function __construct(DetailExternalParcel $detailExternalParcel)
    {
        $this->entity = $detailExternalParcel;
    }

    public function deleteByPackageId(int $packageId): bool
    {
        return $this->entity
            ->where('package_id', $packageId)
            ->delete();
    }
}