<?php

namespace App\Services;

use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\RequestRoomViewRepositoryInterface;
use App\Contracts\Services\HomeServiceInterface;
use App\Models\Enums\NameRole;
use App\Models\User;
use Illuminate\Support\Collection;

class HomeService implements HomeServiceInterface
{
    protected $requestRoomViewRepository;
    protected $inventoryRepository;
    protected $requestRepository;

    public function __construct(RequestRoomViewRepositoryInterface $requestRoomViewRepository,
                                InventoryRepositoryInterface $inventoryRepository,
                                RequestRepositoryInterface $requestRepository)
    {
        $this->requestRoomViewRepository = $requestRoomViewRepository;
        $this->inventoryRepository = $inventoryRepository;
        $this->requestRepository = $requestRepository;
    }

    public function infoCardRequests(User $user): Collection
    {
        $totalNews = $this->requestRoomViewRepository->countNewRequests($user);
        $totalApproved = $this->requestRoomViewRepository->countApprovedRequests($user);
        $totalCancelled = $this->requestRoomViewRepository->countCancelledRequests($user);
        $totalRequests = $this->requestRoomViewRepository->countTotalRequests($user);
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
        return $this->requestRepository->getTotalLast7Days($user);
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