<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\LookupServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\Lookup\LookupResource;
use Illuminate\Http\JsonResponse;

class LookupController extends BaseApiController
{
    private $lookupService;

    public function __construct(LookupServiceInterface $lookupService)
    {
        $this->lookupService = $lookupService;
    }

    public function findAllByType(int $type): JsonResponse
    {
        $lookups = $this->lookupService->findAllByType($type);
        return $this->showAll(LookupResource::collection($lookups));
    }

    public function findByCodeAndType(int $type, string $code): JsonResponse
    {
        $lookup = $this->lookupService->findByCodeAndType($code, $type);
        return $this->showOne(new LookupResource($lookup));
    }
}
