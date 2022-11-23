<?php

namespace App\Core\Contracts;

interface BaseRepositoryInterface
{
    public function create(array $data);

    public function bulkInsert(array $data): bool;

    /**
     * @return void
     */
    public function delete(int $id);

    public function findAll(array $filter = [], string $sort = null, array $columns = ['*']):
    \Illuminate\Database\Eloquent\Collection;

    public function findAllPaginated(array $filters, int $limit, string $sort = null, array $columns = ['*']):
    \Illuminate\Pagination\LengthAwarePaginator;

    public function findById(int $id, array $columns = ['*']);

    public function findRandom();

    public function findRandoms(int $records = 1): \Illuminate\Database\Eloquent\Collection;

    public function sync(int $id, string $relation, array $attributes, bool $detaching = true): array;

    public function update(int $id, array $data);
}