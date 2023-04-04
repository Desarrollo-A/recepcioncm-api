<?php

namespace App\Repositories;

use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Lookup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class LookupRepository extends BaseRepository implements LookupRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Lookup
     */
    protected $entity;

    public function __construct(Lookup $lookup)
    {
        $this->entity = $lookup;
    }

    public function findAllByType(int $type, array $columns = ['*']): Collection
    {
        return $this->entity
            ->where('type', $type)
            ->where('status', true)
            ->orderBy('value')
            ->get($columns);
    }

    public function findByCodeAndType(string $code, int $type): Lookup
    {
        return $this->entity
            ->where('code', $code)
            ->where('type', $type)
            ->where('status', true)
            ->orderBy('value')
            ->firstOrFail();
    }

    public function findByCodeWhereInAndType(array $codes, int $type): Collection
    {
        return $this->entity
            ->whereIn('code', $codes)
            ->where('type', $type)
            ->where('status', true)
            ->orderBy('value')
            ->get();
    }
}