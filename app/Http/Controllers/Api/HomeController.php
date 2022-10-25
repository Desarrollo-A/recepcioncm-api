<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\HomeServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\Home\HomeResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class HomeController extends BaseApiController
{
    public $homeService;

    public function __construct(HomeServiceInterface $homeService)
    {
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST.','.NameRole::APPLICANT);
        $this->homeService = $homeService;
    }

    public function getAllDataHome(): JsonResponse
    {
        $user = auth()->user();
        $totals = $this->homeService->infoCardRequests($user);
        $last7DaysRequests = $this->homeService->getTotalLast7Days($user);
        $totalMonth = $this->homeService->getTotalRequetsOfMonth($user);
        $percentage = $this->homeService->getRequestPercentage($user);
        return $this->showOne(new HomeResource(collect([
            'cards' => $totals,
            'last7DaysRequests' => $last7DaysRequests,
            'totalMonth' => $totalMonth,
            'percentage' => $percentage
        ])));
    }
}
