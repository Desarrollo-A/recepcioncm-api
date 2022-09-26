<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\RoomDTO;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoomServiceInterface extends BaseServiceInterface
{
    public function create(RoomDTO $dto): Room;

    public function update(int $id, RoomDTO $dto): Room;

    /**
     * @return void
     */
    public function changeStatus(int $id, RoomDTO $roomDTO);

    public function findAllPaginatedOffice(Request $request, User $user, array $columns = ['*']): LengthAwarePaginator;

    public function findAllByStateId(int $stateId): Collection;

    /**
     * @return void
     */
    public function updateCode(Room $room);
}