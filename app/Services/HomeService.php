<?php

namespace App\Services;

use App\Contracts\Repositories\DriverPackageScheduleRepositoryInterface;
use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Services\HomeServiceInterface;
use App\Models\Enums\Lookups\StatusCarRequestLookup;
use App\Models\Enums\Lookups\StatusDriverRequestLookup;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use App\Models\Enums\NameRole;
use App\Models\User;
use Illuminate\Support\Collection;

class HomeService implements HomeServiceInterface
{
    protected $inventoryRepository;
    protected $requestRepository;

    protected $driverPackageScheduleRepository;

    public function __construct(InventoryRepositoryInterface $inventoryRepository,
                                RequestRepositoryInterface $requestRepository,
                                DriverPackageScheduleRepositoryInterface $driverPackageScheduleRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
        $this->requestRepository = $requestRepository;
        $this->driverPackageScheduleRepository = $driverPackageScheduleRepository;
    }

    public function infoCardRequests(User $user): Collection
    {
        $totalNews = 0;
        $totalApproved = 0;
        $totalCancelled = 0;
        $totalRequests = 0;
        $roleName = $user->role->name;
        $statusNews = [
            StatusRoomRequestLookup::code(StatusRoomRequestLookup::NEW),
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::NEW),
            StatusDriverRequestLookup::code(StatusDriverRequestLookup::NEW),
            StatusCarRequestLookup::code(StatusCarRequestLookup::NEW)
        ];
        $statusApproved = [
            StatusRoomRequestLookup::code(StatusRoomRequestLookup::APPROVED),
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED),
            StatusDriverRequestLookup::code(StatusDriverRequestLookup::APPROVED),
            StatusCarRequestLookup::code(StatusCarRequestLookup::APPROVED)
        ];
        $statusCancelled = [
            StatusRoomRequestLookup::code(StatusRoomRequestLookup::CANCELLED),
            StatusPackageRequestLookup::code(StatusPackageRequestLookup::CANCELLED),
            StatusDriverRequestLookup::code(StatusDriverRequestLookup::CANCELLED),
            StatusCarRequestLookup::code(StatusCarRequestLookup::CANCELLED)
        ];

        if ($roleName === NameRole::RECEPCIONIST) {
            $totalNews = $this->requestRepository->getTotalRecepcionistByStatus($user->office_id, $statusNews);
            $totalApproved = $this->requestRepository->getTotalRecepcionistByStatus($user->office_id, $statusApproved);
            $totalCancelled = $this->requestRepository->getTotalRecepcionistByStatus($user->office_id, $statusCancelled);
            $totalRequests = $this->requestRepository->getTotalRecepcionistByStatus($user->office_id);
        }
        if ($roleName === NameRole::APPLICANT) {
            $totalNews = $this->requestRepository->getTotalApplicantByStatus($user->id, $statusNews);
            $totalApproved = $this->requestRepository->getTotalApplicantByStatus($user->id, $statusApproved);
            $totalCancelled = $this->requestRepository->getTotalApplicantByStatus($user->id, $statusCancelled);
            $totalRequests = $this->requestRepository->getTotalApplicantByStatus($user->id);
        }
        if ($roleName === NameRole::DRIVER) {
            $totalApproved = $this->driverPackageScheduleRepository->getTotalByStatus($user->id, [
                StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED)
            ]);
            $totalRequests = $this->driverPackageScheduleRepository->getTotalByStatus($user->id);
        }

        return collect([
            'news' => $totalNews,
            'approved' => $totalApproved,
            'cancelled' => $totalCancelled,
            'requests' => $totalRequests
        ]);
    }

    public function getTotalLast7Days(User $user): array
    {
        if (in_array($user->role->name, [NameRole::APPLICANT, NameRole::DRIVER])) {
            return [];
        }
        return $this->requestRepository->getTotalLast7Days($user->office_id);
    }

    public function getTotalRequetsOfMonth(User $user): int
    {
        if (in_array($user->role->name, [NameRole::APPLICANT, NameRole::DRIVER])) {
            return 0;
        }
        return $this->requestRepository->getTotalRequetsOfMonth($user->office_id);
    }

    public function getRequestPercentage(User $user): int
    {
        if (in_array($user->role->name, [NameRole::APPLICANT, NameRole::DRIVER])) {
            return 0;
        }

        $totalLastMonth = $this->requestRepository->getTotalRequetsOfLastMonth($user->office_id);
        if ($totalLastMonth === 0) {
            return 0;
        }
        $totalMonth = $this->requestRepository->getTotalRequetsOfMonth($user->office_id);
        $result = $totalMonth - $totalLastMonth;
        return ($result * 100) / $totalLastMonth;
    }
}