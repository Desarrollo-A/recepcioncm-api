<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\CalendarServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\Calendar\CalendarResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class CalendarController extends BaseApiController
{
    private $calendarService;

    public function __construct(CalendarServiceInterface $calendarService)
    {
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST.','.NameRole::APPLICANT);
        $this->calendarService = $calendarService;
    }

    public function findAll(): JsonResponse
    {
        $data = $this->calendarService->getDataCalendar(auth()->user());
        return $this->showAll(CalendarResource::collection($data));
    }
}
