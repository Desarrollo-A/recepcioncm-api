<?php

namespace App\Core;

use App\Core\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder
     */
    protected $entity;

    /**
     * @throws \Throwable
     * @return mixed
     */
    public function create(array $data)
    {
        $entity = $this->entity->newInstance($data);
        $entity->saveOrFail();
        return $entity;
    }

    public function bulkInsert(array $data): bool
    {
        return $this->entity->insert($data);
    }

    /**
     * @throws \Throwable
     */
    public function delete(int $id): void
    {
        $entity = $this->findById($id);
        $entity->delete();
    }

    public function bulkDelete(array $ids): bool
    {
        return $this->entity->whereIn('id', $ids)->delete();
    }

    /**
     * @throws \Throwable
     */
    public function findAll(array $filter = [], string $sort = null, array $columns = ['*']): Collection
    {
        return $this->entity
            ->filter($filter)
            ->applySort($sort)
            ->get($columns);
    }

    /**
     * @throws \Throwable
     */
    public function findAllPaginated(array $filters, int $limit, string $sort = null, array $columns = ['*']):
    LengthAwarePaginator
    {
        return $this->entity
            ->filter($filters)
            ->applySort($sort)
            ->paginate($limit, $columns);
    }

    /**
     * @throws \Throwable
     */
    public function findById(int $id, array $columns = ['*'])
    {
        return $this->entity->findOrFail($id, $columns);
    }

    public function findRandom()
    {
        return $this->entity->inRandomOrder()->limit(1)->first();
    }

    public function findRandoms(int $records = 1): Collection
    {
        return $this->entity->inRandomOrder()->limit($records)->get();
    }

    /**
     * @throws \Throwable
     */
    public function sync(int $id, string $relation, array $attributes, bool $detaching = true): array
    {
        return $this->findById($id)->{$relation}()->sync($attributes, $detaching);
    }

    /**
     * @throws \Throwable
     */
    public function update(int $id, array $data)
    {
        $entity = $this->findById($id);
        $entity->fill($data);
        $entity->saveOrFail();
        return $entity;
    }
}