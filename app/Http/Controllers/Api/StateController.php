<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\StateServiceInterface;
use App\Core\BaseApiController;
use App\Helpers\Cache;
use App\Helpers\Enum\CacheKey;
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

    /**
     * @throws \Exception
     */
    public function getAll(): JsonResponse
    {
        return \cache()->remember(Cache::getKey(CacheKey::FIND_ALL_STATES), now()->addDay(), function () {
            $states = $this->stateService->getAll();
            return $this->showAll(StateResource::collection($states));
        });
    }
}
