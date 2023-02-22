<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\UserDTO;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method User findById(int $id)
 */
interface UserServiceInterface extends BaseServiceInterface
{
    public function create(UserDTO $dto);

    public function findAllPaginatedWithoutUser(Request $request, int $userId, array $columns = ['*']): LengthAwarePaginator;

    /**
     * @return void
     */
    public function changeStatus(int $id, UserDTO $dto);

    public function removeOldTokens(): void;

    public function storeDriver(UserDTO $dto): User;

    public function update(int $id, UserDTO $dto): User;
}