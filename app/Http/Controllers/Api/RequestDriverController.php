<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\RequestDriverServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\RequestDriver\StoreRequestDriverRequest;
use App\Http\Requests\RequestDriver\UploadFileDriverRequest;
use App\Http\Resources\RequestDriver\RequestDriverResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class RequestDriverController extends BaseApiController
{
    private $requestDriverService;

    public function __construct(RequestDriverServiceInterface $requestDriverService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'uploadAuthorizationFile');
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
}
