<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\DriverServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\Driver\DriverCollection;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends BaseApiController
{
    private $driverService;

    public function __construct(DriverServiceInterface $driverService)
    {
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST);
        $this->driverService = $driverService;
    }

    public function index(Request $request): JsonResponse
    {
        $officeId = auth()->user()->office_id;
        $drivers = $this->driverService->findAllPaginatedOffice($officeId, $request);
        return $this->showAll(new DriverCollection($drivers, true));
    }
}
