<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\RequestDTO;
use App\Models\Dto\RequestRoomDTO;
use App\Models\RequestRoom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface RequestRoomServiceInterface extends BaseServiceInterface
{
    public function create(RequestRoomDTO $dto): RequestRoom;

    public function findAllRoomsPaginated(Request $request, User $user, array $columns = ['*']): LengthAwarePaginator;

    public function isAvailableSchedule(Carbon $startDate, Carbon $endDate): bool;

    public function assignSnack(RequestRoomDTO $dto, int $officeId): \App\Models\Request;

    public function getStatusByStatusCurrent(string $code, string $roleName): Collection;

    public function findByRequestId(int $requestId, User $user): RequestRoom;

    public function cancelRequest(CancelRequestDTO $dto, User $user): \App\Models\Request;

    public function getAvailableScheduleByDay(int $requestId, Carbon $date): \Illuminate\Support\Collection;

    public function proposalRequest(int $requestId, RequestDTO $dto): \App\Models\Request;

    public function withoutAttendingRequest(int $requestId): \App\Models\Request;
}