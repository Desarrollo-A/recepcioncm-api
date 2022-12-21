<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\RequestCarServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\RequestCar\StoreRequestCarRequest;
use App\Http\Requests\RequestCar\UploadFileRequestCarRequest;
use App\Http\Resources\RequestCar\RequestCarResource;
use App\Http\Resources\RequestCar\RequestCarViewCollection;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestCarController extends BaseApiController
{
    private $requestCarService;

    public function __construct(RequestCarServiceInterface $requestCarService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'uploadAuthorizationFile', 'deleteRequestCar');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'store', 'uploadAuthorizationFile', 'show');
        $this->requestCarService = $requestCarService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $requestCars = $this->requestCarService->findAllCarsPaginated($request, $user);
        return $this->showAll(new RequestCarViewCollection($requestCars, true));
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

    public function deleteRequestCar(int $requestId): JsonResponse
    {
        $this->requestCarService->deleteRequestCar($requestId, auth()->user());
        return $this->noContentResponse();
    }

    public function show(int $requestId): JsonResponse
    {
        $requestCar = $this->requestCarService->findByRequestId($requestId, auth()->user());
        return $this->showOne(new RequestCarResource($requestCar));
    }
}
