<?php

namespace App\Services;

use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\RoomRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\RoomServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\QueryParam;
use App\Helpers\Validation;
use App\Models\Dto\RoomDTO;
use App\Models\Enums\Lookups\StatusRoomLookup;
use App\Models\Enums\TypeLookup;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RoomService extends BaseService implements RoomServiceInterface
{
    protected $entityRepository;
    protected $lookupRepository;
    protected $userRepository;

    public function __construct(RoomRepositoryInterface $roomRepository,
                                LookupRepositoryInterface $lookupRepository,
                                UserRepositoryInterface $userRepository)
    {
        $this->entityRepository = $roomRepository;
        $this->lookupRepository = $lookupRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(RoomDTO $dto): Room
    {
        $dto->status_id = $this->lookupRepository->findByCodeAndType(StatusRoomLookup::code(StatusRoomLookup::ACTIVE),
            TypeLookup::STATUS_ROOM)->id;
        $room = $this->entityRepository->create($dto->toArray(['name', 'office_id', 'no_people', 'recepcionist_id',
            'status_id']));

        return $room->fresh('status', 'office');
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, RoomDTO $dto): Room
    {
        $room = $this->entityRepository->update($id, $dto->toArray(['name', 'no_people']));
        return $room->fresh('status', 'office');
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function changeStatus(int $id, RoomDTO $roomDTO)
    {
        $this->entityRepository->update($id, $roomDTO->toArray(['status_id']));
    }

    /**
     * @throws \App\Exceptions\CustomErrorException
     */
    public function findAllPaginatedOffice(Request $request, User $user, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->entityRepository->findAllPaginatedOffice($user, $filters, $perPage, $sort, $columns);
    }

    public function findAllByStateId(int $stateId): Collection
    {
        $activeId = $this->lookupRepository->findByCodeAndType(StatusRoomLookup::code(StatusRoomLookup::ACTIVE),
            TypeLookup::STATUS_ROOM)->id;
        return $this->entityRepository->findAllByStateId($stateId, $activeId);
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function updateCode(Room $room)
    {
        $roomDTO = new RoomDTO(['code' => Room::INITIAL_CODE.$room->id]);
        $this->entityRepository->update($room->id, $roomDTO->toArray(['code']));
    }
}