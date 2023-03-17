<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\LookupServiceInterface;
use App\Contracts\Services\RoomServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use App\Http\Requests\Room\ChangeStatusRoomRequest;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Http\Resources\Room\RoomCollection;
use App\Http\Resources\Room\RoomResource;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends BaseApiController
{
    private $roomService;
    private $lookupService;

    public function __construct(RoomServiceInterface $roomService,
                                LookupServiceInterface $lookupService)
    {
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('store', 'update', 'index', 'destroy');
        $this->middleware('role.permission:'.NameRole::ADMIN.','.NameRole::RECEPCIONIST)
            ->only('show', 'changeStatus');
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('findAllByStateId');

        $this->roomService = $roomService;
        $this->lookupService = $lookupService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $rooms = $this->roomService->findAllPaginatedOffice($request, $user);
        return $this->showAll(new RoomCollection($rooms, true));
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        $roomDTO = $request->toDTO();
        $room = $this->roomService->create($roomDTO);
        return $this->showOne(new RoomResource($room));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, UpdateRoomRequest $request): JsonResponse
    {
        $roomDTO = $request->toDTO();
        if ($id !== $roomDTO->id) {
            throw new CustomErrorException(Message::INVALID_ID_PARAMETER_WITH_ID_BODY, Response::HTTP_BAD_REQUEST);
        }
        $room = $this->roomService->update($id, $roomDTO);
        return $this->showOne(new RoomResource($room));
    }

    public function destroy(int $id): \Illuminate\Http\Response
    {
        $this->roomService->delete($id);
        return $this->noContentResponse();
    }

    public function show(int $id): JsonResponse
    {
        $room = $this->roomService->findById($id);
        return $this->showOne(new RoomResource($room));
    }

    /**
     * @throws CustomErrorException
     */
    public function changeStatus(int $id, ChangeStatusRoomRequest $request): \Illuminate\Http\Response
    {
        $roomDTO = $request->toDTO();
        $this->lookupService->validateLookup($roomDTO->status_id, TypeLookup::STATUS_ROOM, 'Estatus no válido.');
        $this->roomService->changeStatus($id, $roomDTO);
        return $this->noContentResponse();
    }

    public function findAllByStateId(int $stateId): JsonResponse
    {
        $rooms = $this->roomService->findAllByStateId($stateId);
        return $this->showAll(new RoomCollection($rooms));
    }
}
