<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\RequestDriverServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\RequestDriver\StoreRequestDriverRequest;
use App\Http\Requests\RequestDriver\UploadFileDriverRequest;
use App\Http\Resources\RequestDriver\RequestDriverResource;
use App\Http\Resources\RequestDriver\RequestDriverViewCollection;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestDriverController extends BaseApiController
{
    private $requestDriverService;

    public function __construct(RequestDriverServiceInterface $requestDriverService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'uploadAuthorizationFile');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'show');
        $this->requestDriverService = $requestDriverService;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreRequestDriverRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $requestDriver = $this->requestDriverService->create($dto);
        return $this->showOne(new RequestDriverResource($requestDriver));
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadAuthorizationFile(int $requestId, UploadFileDriverRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $this->requestDriverService->uploadAuthorizationFile($requestId, $dto);
        return $this->noContentResponse();
    }

    public function index(Request $request): JsonResponse
    {
        $requestDrivers = $this->requestDriverService->findAllDriversPaginated($request, auth()->user());
        return $this->showAll(new RequestDriverViewCollection($requestDrivers , true));
    }

    public function show(int $requestId): JsonResponse
    {
        $requestDriver = $this->requestDriverService->findById($requestId);
        return $this->showOne(new RequestDriverResource($requestDriver));
    }
}
