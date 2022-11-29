<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\RequestPackageServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\Request\StarRatingRequest;
use App\Http\Requests\RequestPackage\StoreRequestPackageRequest;
use App\Http\Requests\RequestPackage\UploadFileRequestPackageRequest;
use App\Http\Requests\RequestRoom\CancelRequestRoomRequest;
use App\Http\Resources\Lookup\LookupResource;
use App\Http\Resources\Package\PackageResource;
use App\Http\Resources\RequestPackage\RequestPackageViewCollection;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpCodes;


class RequestPackageController extends BaseApiController
{
    private $requestPackageService;

    public function __construct(RequestPackageServiceInterface $requestPackageService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('store', 'uploadAuthorizationFile');
        $this->middleware('role.permission:'.NameRole::APPLICANT.','.NameRole::RECEPCIONIST)
            ->only('index', 'show', 'getStatusByStatusCurrent', 'cancelRequest');
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
    public function uploadAuthorizationFile(int $requestId, UploadFileRequestPackageRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $this->requestPackageService->uploadAuthorizationFile($requestId, $dto);
        return $this->noContentResponse();
    }

    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $requestPackages = $this->requestPackageService->findAllRoomsPaginated($request, $user);
        return $this->showAll(new RequestPackageViewCollection($requestPackages, true));
    }

    public function show(int $requestId): JsonResponse
    {
        $package = $this->requestPackageService->findById($requestId);
        return $this->showOne(new PackageResource($package));
    }

    public function insertScore(StarRatingRequest $request): JsonResponse
    {
        $scoreDTO = $request->toDTO();
        $this->requestPackageService->insertScore($scoreDTO);
        return $this->noContentResponse();
    }

    public function isPackageCompleted(int $requestPackageId): JsonResponse
    {
        $requests = $this->requestPackageService->isPackageCompleted($requestPackageId);
        return $this->successResponse(['deliveredPackage' => $requests], HttpCodes::HTTP_OK);
    }

    public function getStatusByStatusCurrent(string $code): JsonResponse
    {
        $roleName = auth()->user()->role->name;
        $status = $this->requestPackageService->getStatusByStatusCurrent($code, $roleName);
        return $this->showAll(LookupResource::collection($status));
    }

    /**
     * @throws CustomErrorException
     */
    public function cancelRequest(int $requestId, CancelRequestRoomRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $dto->request_id = $requestId;
        $this->requestPackageService->cancelRequest($dto);
        return $this->noContentResponse();
    }
}
