<?php

namespace App\Core\Contracts;

interface IBaseRepository
{
    public function create(array $data): \Illuminate\Database\Eloquent\Model;

    /**
     * @return void
     */
    public function delete(int $id);

    public function findAll(array $filter = [], string $sort = null, array $columns = ['*']):
    \Illuminate\Database\Eloquent\Collection;

    public function findAllPaginated(array $filters, int $limit, string $sort = null, array $columns = ['*']):
    \Illuminate\Pagination\LengthAwarePaginator;

    public function findById(int $id, array $columns = ['*']): \Illuminate\Database\Eloquent\Model;

    public function findRandom(): \Illuminate\Database\Eloquent\Model;

    public function findRandoms(int $records = 1): \Illuminate\Database\Eloquent\Collection;

    public function update(int $id, array $data): \Illuminate\Database\Eloquent\Model;
}