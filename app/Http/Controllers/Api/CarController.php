<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\CarServiceInterface;
use App\Contracts\Services\LookupServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use App\Http\Requests\Car\ChangeStatusCarRequest;
use App\Http\Requests\Car\StoreCarRequest;
use App\Http\Requests\Car\UpdateCarRequest;
use App\Http\Resources\Car\CarCollection;
use App\Http\Resources\Car\CarResource;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpCodes;

class CarController extends BaseApiController
{
    private $carService;
    private $lookupService;

    public function __construct(CarServiceInterface $carService,
                                LookupServiceInterface $lookupService)
    {
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST);
        $this->carService = $carService;
        $this->lookupService = $lookupService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $cars = $this->carService->findAllPaginatedOffice($request, $user);
        return $this->showAll(new CarCollection($cars, true));
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreCarRequest $request): JsonResponse
    {
        $carDTO = $request->toDTO();
        $car = $this->carService->create($carDTO);
        return $this->showOne(new CarResource($car));
    }

    public function show(int $id): JsonResponse
    {
        $car = $this->carService->findById($id);
        return $this->showOne(new CarResource($car));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, UpdateCarRequest $request): JsonResponse
    {
        $carDTO = $request->toDTO();
        if ($id !== $carDTO->id) {
            throw new CustomErrorException(Message::INVALID_ID_PARAMETER_WITH_ID_BODY, HttpCodes::HTTP_BAD_REQUEST);
        }
        $car = $this->carService->update($id, $carDTO);
        return $this->showOne(new CarResource($car));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->carService->delete($id);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function changeStatus(int $id, ChangeStatusCarRequest $request): JsonResponse
    {
        $carDTO = $request->toDTO();
        $this->lookupService->validateLookup($carDTO->status_id, TypeLookup::STATUS_CAR, 'Estatus no válido');
        $this->carService->changeStatus($id, $carDTO);
        return $this->noContentResponse();
    }
}