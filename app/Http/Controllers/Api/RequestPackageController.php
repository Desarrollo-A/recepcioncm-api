<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\RequestPackageServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\Request\StarRatingRequest;
use App\Http\Requests\RequestPackage\StoreRequestPackageRequest;
use App\Http\Requests\RequestPackage\UploadFileRequestPackageRequest;
use App\Http\Resources\Package\PackageResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;

class RequestPackageController extends BaseApiController
{
    private $requestPackageService;

    public function __construct(RequestPackageServiceInterface $requestPackageService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)->except('insertScore');
        $this->requestPackageService = $requestPackageService;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreRequestPackageRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $package = $this->requestPackageService->createRequestPackage($dto);
        return $this->showOne(new PackageResource($package));
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadAuthorizationFile(int $id, UploadFileRequestPackageRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $this->requestPackageService->uploadAuthorizationFile($id, $dto);
        return $this->noContentResponse();
    }

    public function insertScore(StarRatingRequest $request): JsonResponse
    {
        $scoreDTO = $request->toDTO();
        $this->requestPackageService->insertScore($scoreDTO);
        return $this->noContentResponse();
    }
}
