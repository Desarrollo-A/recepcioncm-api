<?php

namespace App\Services;

use App\Contracts\Repositories\DriverPackageScheduleRepositoryInterface;
use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Services\DashboardServiceInterface;
use App\Helpers\Utils;
use App\Models\Enums\Lookups\StatusPackageRequestLookup;
use App\Models\Enums\NameRole;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardService implements DashboardServiceInterface
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

        if ($roleName === NameRole::RECEPCIONIST) {
            $totalNews = $this->requestRepository->getTotalRecepcionistByStatus($user->office_id, Utils::getStatusNewsRequest());
            $totalApproved = $this->requestRepository->getTotalRecepcionistByStatus($user->office_id, Utils::getStatusApprovedRequest());
            $totalCancelled = $this->requestRepository->getTotalRecepcionistByStatus($user->office_id, Utils::getStatusCancelledRequests());
            $totalRequests = $this->requestRepository->getTotalRecepcionistByStatus($user->office_id);
        }
        if ($roleName === NameRole::APPLICANT) {
            $totalNews = $this->requestRepository->getTotalApplicantByStatus($user->id, Utils::getStatusNewsRequest());
            $totalApproved = $this->requestRepository->getTotalApplicantByStatus($user->id, Utils::getStatusApprovedRequest());
            $totalCancelled = $this->requestRepository->getTotalApplicantByStatus($user->id, Utils::getStatusCancelledRequests());
            $totalRequests = $this->requestRepository->getTotalApplicantByStatus($user->id);
        }
        if ($roleName === NameRole::DRIVER) {
            $totalApproved = $this->driverPackageScheduleRepository->getTotalByStatus($user->id, [
                StatusPackageRequestLookup::code(StatusPackageRequestLookup::APPROVED)
            ]);
            $totalRequests = $this->driverPackageScheduleRepository->getTotalByStatus($user->id);
        }
        if ($roleName === NameRole::DEPARTMENT_MANAGER) {
            $totalNews = $this->requestRepository->getTotalManagerRequestPackagesByStatus(
                $user->id, [StatusPackageRequestLookup::code(StatusPackageRequestLookup::IN_REVIEW_MANAGER)]
            );
            $totalRequests = $this->requestRepository->getTotalManagerRequestPackagesByStatus($user->id);
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
        if (in_array($user->role->name, [NameRole::APPLICANT, NameRole::DRIVER, NameRole::DEPARTMENT_MANAGER])) {
            return [];
        }
        return $this->requestRepository->getTotalLast7Days($user->office_id);
    }

    public function getTotalRequetsOfMonth(User $user): int
    {
        if (in_array($user->role->name, [NameRole::APPLICANT, NameRole::DRIVER, NameRole::DEPARTMENT_MANAGER])) {
            return 0;
        }
        return $this->requestRepository->getTotalRequetsOfMonth($user->office_id);
    }

    public function getRequestPercentage(User $user): int
    {
        if (in_array($user->role->name, [NameRole::APPLICANT, NameRole::DRIVER, NameRole::DEPARTMENT_MANAGER])) {
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