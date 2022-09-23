<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|User
     */
    protected $entity;

    public function __construct(User $user)
    {
        $this->entity = $user;
    }

    public function findById(int $id, array $columns = ['*']): User
    {
        return $this->entity
            ->with(['status', 'role', 'office'])
            ->findOrFail($id, $columns);
    }

    public function findByNoEmployee(string $noEmployee): User
    {
        return $this->entity
            ->with(['status', 'role'])
            ->where('no_employee', $noEmployee)
            ->firstOrFail();
    }
}