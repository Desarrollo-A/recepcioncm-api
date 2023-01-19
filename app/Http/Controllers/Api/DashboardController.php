<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\DashboardServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\Home\HomeResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class DashboardController extends BaseApiController
{
    public $dashboardService;

    public function __construct(DashboardServiceInterface $dashboardService)
    {
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST.','.NameRole::APPLICANT.','.NameRole::DRIVER);
        $this->dashboardService = $dashboardService;
    }

    public function getAllDataHome(): JsonResponse
    {
        $user = auth()->user();
        $totals = $this->dashboardService->infoCardRequests($user);
        $last7DaysRequests = $this->dashboardService->getTotalLast7Days($user);
        $totalMonth = $this->dashboardService->getTotalRequetsOfMonth($user);
        $percentage = $this->dashboardService->getRequestPercentage($user);
        return $this->showOne(new HomeResource(collect([
            'cards' => $totals,
            'last7DaysRequests' => $last7DaysRequests,
            'totalMonth' => $totalMonth,
            'percentage' => $percentage
        ])));
    }
}
