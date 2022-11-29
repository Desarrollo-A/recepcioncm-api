<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\DriverServiceInterface;
use App\Core\BaseApiController;
use App\Http\Requests\DriverCar\DriverCarRequest;
use App\Http\Resources\Driver\DriverCollection;
use App\Http\Resources\Driver\DriverResource;
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

    public function insertDriverCar(DriverCarRequest $request): JsonResponse
    {   
        $driverCarDTO = $request->toDto();
        $this->driverService->insertDriverCar($driverCarDTO->car_id, $driverCarDTO->driver_id);
        return $this->noContentResponse();
    }

    public function show(int $driverId): JsonResponse
    {
        $conductor = $this->driverService->findById($driverId);
        return $this->showOne(new DriverResource($conductor));
    }

    public function findAllByOfficeId(): JsonResponse
    {
        $drivers = $this->driverService->findAllByOfficeId(auth()->user()->office_id);
        return $this->showAll(DriverResource::collection($drivers));
    }
}
