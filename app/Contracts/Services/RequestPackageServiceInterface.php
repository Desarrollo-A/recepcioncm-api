<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\PackageDTO;
use App\Models\Dto\ScoreDTO;
use App\Models\Package;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;

interface RequestPackageServiceInterface extends BaseServiceInterface
{
    public function createRequestPackage(PackageDTO $dto): Package;

    public function uploadAuthorizationFile(int $id, PackageDTO $dto): void;

    public function findAllRoomsPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator;

    public function insertScore(ScoreDTO $score): void;

    public function isPackageCompleted(int $requestPackageId): bool;

    public function getStatusByStatusCurrent(string $code, string $roleName): Collection;

    public function cancelRequest(CancelRequestDTO $dto): Request;

    public function transferRequest(int $packageId, PackageDTO $dto): void;
}