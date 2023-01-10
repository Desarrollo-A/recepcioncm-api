<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\OfficeServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\Office\OfficeResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class OfficeController extends BaseApiController
{
    private $officeService;

    public function __construct(OfficeServiceInterface $officeService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('getOfficeByStateWithDriver', 'getOfficeByStateWithDriverAndCar');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('getByStateWithDriverWithoutOffice', 'getOfficeByStateWithDriverAndCarWithoutOffice',
                'getOfficeByStateWithCarWithoutOffice', 'getOfficeByStateWithCar');
        $this->officeService = $officeService;
    }

    public function getOfficeByStateWithDriver(int $stateId): JsonResponse
    {
        $offices = $this->officeService->getOfficeByStateWithDriver($stateId);
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function getByStateWithDriverWithoutOffice(int $officeId): JsonResponse
    {
        $offices = $this->officeService->getByStateWithDriverWithoutOffice($officeId);
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function getOfficeByStateWithDriverAndCar(int $stateId, int $noPeople): JsonResponse
    {
        $offices = $this->officeService->getOfficeByStateWithDriverAndCar($stateId, $noPeople);
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function getOfficeByStateWithCar(int $stateId, int $noPeople): JsonResponse
    {
        $offices = $this->officeService->getOfficeByStateWithCar($stateId, $noPeople);
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function getOfficeByStateWithDriverAndCarWithoutOffice(int $officeId, int $noPeople): JsonResponse
    {
        $offices = $this->officeService->getOfficeByStateWithDriverAndCarWithoutOffice($officeId, $noPeople);
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function getOfficeByStateWithCarWithoutOffice(int $officeId, int $noPeople): JsonResponse
    {
        $offices = $this->officeService->getOfficeByStateWithCarWithoutOffice($officeId, $noPeople);
        return $this->showAll(OfficeResource::collection($offices));
    }
}
