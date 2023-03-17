<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\OfficeServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use App\Http\Requests\Office\StoreOfficeRequest;
use App\Http\Requests\Office\UpdateOfficeRequest;
use App\Http\Resources\Office\OfficeCollection;
use App\Http\Resources\Office\OfficeResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OfficeController extends BaseApiController
{
    private $officeService;

    public function __construct(OfficeServiceInterface $officeService)
    {
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('getOfficeByStateWithDriver', 'getOfficeByStateWithDriverAndCar', 'getOfficeByStateWithCar');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('getByStateWithDriverWithoutOffice', 'getOfficeByStateWithDriverAndCarWithoutOffice',
                'getOfficeByStateWithCarWithoutOffice');
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST. ','. NameRole::APPLICANT. ','.
            NameRole::ADMIN)
            ->only('getAll');
        $this->middleware('role.permission:' . NameRole::ADMIN)
            ->only('index', 'store');

        $this->officeService = $officeService;
    }

    public function getOfficeByStateWithDriver(int $stateId): JsonResponse
    {
        $offices = $this->officeService->getOfficeByStateWithDriver($stateId);
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function getByStateWithDriverWithoutOffice(int $officeId): JsonResponse
    {
        $offices = $this->officeService->getByStateWithDriverWithoutOffice($officeId);
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function getOfficeByStateWithDriverAndCar(int $stateId, int $noPeople): JsonResponse
    {
        $offices = $this->officeService->getOfficeByStateWithDriverAndCar($stateId, $noPeople);
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function getOfficeByStateWithCar(int $stateId, int $noPeople): JsonResponse
    {
        $offices = $this->officeService->getOfficeByStateWithCar($stateId, $noPeople);
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function getOfficeByStateWithDriverAndCarWithoutOffice(int $officeId, int $noPeople): JsonResponse
    {
        $offices = $this->officeService->getOfficeByStateWithDriverAndCarWithoutOffice($officeId, $noPeople);
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function getOfficeByStateWithCarWithoutOffice(int $officeId, int $noPeople): JsonResponse
    {
        $offices = $this->officeService->getOfficeByStateWithCarWithoutOffice($officeId, $noPeople);
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function getAll(): JsonResponse
    {
        $offices = $this->officeService->findAll();
        return $this->showAll(OfficeResource::collection($offices));
    }

    public function index(Request $request): JsonResponse
    {
        $offices = $this->officeService->findAllPaginated($request);
        return $this->showAll(new OfficeCollection($offices, true));
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreOfficeRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $office = $this->officeService->store($dto);
        return $this->showOne(new OfficeResource($office));
    }

    public function show(int $id): JsonResponse
    {
        $office = $this->officeService->findById($id);
        return $this->showOne(new OfficeResource($office));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, UpdateOfficeRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        if ($id !== $dto->id) {
            throw new CustomErrorException(Message::INVALID_ID_PARAMETER_WITH_ID_BODY, Response::HTTP_BAD_REQUEST);
        }
        $office = $this->officeService->update($id, $dto);
        return $this->showOne(new OfficeResource($office));
    }

    public function destroy(int $id): \Illuminate\Http\Response
    {
        $this->officeService->delete($id);
        return $this->noContentResponse();
    }
}
