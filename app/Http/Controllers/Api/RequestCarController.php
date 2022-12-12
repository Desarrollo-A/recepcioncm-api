<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\RequestCarServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\RequestCar\StoreRequestCarRequest;
use App\Http\Requests\RequestCar\UploadFileRequestCarRequest;
use App\Http\Resources\RequestCar\RequestCarResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class RequestCarController extends BaseApiController
{
    private $requestCarService;

    public function __construct(RequestCarServiceInterface $requestCarService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'uploadAuthorizationFile');
        $this->requestCarService = $requestCarService;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreRequestCarRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $requestCar = $this->requestCarService->create($dto);
        return $this->showOne(new RequestCarResource($requestCar));
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadAuthorizationFile(int $requestId, UploadFileRequestCarRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $this->requestCarService->uploadAuthorizationFile($requestId, $dto);
        return $this->noContentResponse();
    }
}
