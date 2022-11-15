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
        $this->middleware('role.permission:'.NameRole::APPLICANT);
        $this->officeService = $officeService;
    }

    public function getOfficeByStateWithDriver(int $stateId): JsonResponse
    {
        $offices = $this->officeService->getOfficeByStateWithDriver($stateId);
        return $this->showAll(OfficeResource::collection($offices));
    }
}
