<?php

namespace App\Repositories;

use App\Contracts\Repositories\OfficeManagerRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\OfficeManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class OfficeManagerRepository extends BaseRepository implements OfficeManagerRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|OfficeManager
     */
    protected $entity;

    public function __construct(OfficeManager $officeManager)
    {
        $this->entity = $officeManager;
    }

    public function deleteByManagerId(int $userId): bool
    {
        return $this->entity
            ->where('manager_id', $userId)
            ->delete();
    }
}