<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\StateServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\State\StateResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class StateController extends BaseApiController
{
    private $stateService;

    public function __construct(StateServiceInterface $stateService)
    {
        $this->middleware('role.permission:'.NameRole::ADMIN.','.NameRole::RECEPCIONIST.','
            .NameRole::APPLICANT);
        $this->stateService = $stateService;
    }

    public function getAll(): JsonResponse
    {
        $states = $this->stateService->getAll();
        return $this->showAll(StateResource::collection($states));
    }
}
