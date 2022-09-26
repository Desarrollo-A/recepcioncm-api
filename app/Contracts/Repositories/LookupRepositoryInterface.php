<?php

namespace App\Contracts\Repositories;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Models\Lookup;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method Lookup findById(int $id, array $columns = ['*'])
 */
interface LookupRepositoryInterface extends BaseRepositoryInterface
{
    public function findAllByType(int $type, array $columns = ['*']): Collection;

    public function findByCodeAndType(string $code, int $type): Lookup;

    public function findByCodeWhereInAndType(array $codes, int $type): Collection;
}