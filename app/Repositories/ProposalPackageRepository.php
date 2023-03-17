<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProposalPackageRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\ProposalPackage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ProposalPackageRepository extends BaseRepository implements ProposalPackageRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|ProposalPackage
     */
    protected $entity;

    /**
     * @param ProposalPackage $entity
     */
    public function __construct(ProposalPackage $entity)
    {
        $this->entity = $entity;
    }
}