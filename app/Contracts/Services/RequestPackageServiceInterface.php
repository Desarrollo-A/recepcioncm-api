<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use App\Models\DeliveredPackage;
use App\Models\Dto\CancelRequestDTO;
use App\Models\Dto\DeliveredPackageDTO;
use App\Models\Dto\PackageDTO;
use App\Models\Dto\ProposalRequestDTO;
use App\Models\Dto\RequestDTO;
use App\Models\Dto\ScoreDTO;
use App\Models\Package;
use App\Models\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface RequestPackageServiceInterface extends BaseServiceInterface
{
    public function createRequestPackage(PackageDTO $dto): Package;

    /**
     * @param User|Authenticatable $user
     */
    public function findAllPackagesPaginated(HttpRequest $request, User $user, array $columns = ['*']): LengthAwarePaginator;

    public function insertScore(ScoreDTO $score): void;

    public function isPackageCompleted(int $requestPackageId): bool;

    public function getStatusByStatusCurrent(string $code, string $roleName): Collection;

    public function cancelRequest(CancelRequestDTO $dto): object;

    public function transferRequest(int $packageId, PackageDTO $dto): Package;

    public function getScheduleDriver(int $officeId): Collection;

    public function getPackagesByDriverId(int $driverId, Carbon $date): Collection;

    public function approvedRequest(PackageDTO $dto): Package;

    public function isAuthPackage(string $authCodePackage): bool;

    public function findByRequestId(int $requestId): Package;

    public function onRoad(int $requestId): Request;

    /**
     * @param User|Authenticatable $user
     */
    public function findByPackageRequestId(int $requestId, User $user): Package;

    public function findAllByDateAndOffice(int $officeId, Carbon $date): Collection;

    public function responseRejectRequest(int $requestId, RequestDTO $dto): Request;

    /**
     * @param User|Authenticatable $user
     */
    public function findAllByDriverIdPaginated(HttpRequest $request, User $user,
                                               array       $columns = ['*']): LengthAwarePaginator;

    /**
     * @param User|Authenticatable $user
     */
    public function findAllDeliveredByDriverIdPaginated(HttpRequest $request, User $user,
                                                        array $columns = ['*']): LengthAwarePaginator;

    public function deliveredPackage(DeliveredPackageDTO $dto): Request;

    public function deliveredRequestSignature(int $packageId, DeliveredPackageDTO $dto): void;

    public function reportRequestPackagePdf (HttpRequest $request, int $driverId);

    public function reportRequestPackageExcel(HttpRequest $request, int $driverId): StreamedResponse;

    public function acceptCancelPackage(int $requestId, RequestDTO $dto): Request;

    public function findAllPackagesByManagerIdPaginated(HttpRequest $request, int $departmentManagerId,
                                                        array $columns = ['*']): LengthAwarePaginator;
}