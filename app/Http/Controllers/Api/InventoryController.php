<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\InventoryServiceInterface;
use App\Contracts\Services\LookupServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use App\Http\Requests\Inventory\StoreInventoryRequest;
use App\Http\Requests\Inventory\UpdateImageInventoryRequest;
use App\Http\Requests\Inventory\UpdateInventoryRequest;
use App\Http\Requests\Inventory\UpdateStockInventoryRequest;
use App\Http\Resources\Inventory\InventoryCollection;
use App\Http\Resources\Inventory\InventoryResource;
use App\Models\Dto\InventoryDTO;
use App\Models\Enums\NameRole;
use App\Models\Enums\TypeLookup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InventoryController extends BaseApiController
{
    private $inventoryService;
    private $lookupService;

    public function __construct(InventoryServiceInterface $inventoryService,
                                LookupServiceInterface $lookupService)
    {
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST);
        $this->inventoryService = $inventoryService;
        $this->lookupService = $lookupService;
    }

    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        $inventories = $this->inventoryService->findAllPaginatedOffice($request, $user);
        return $this->showAll(new InventoryCollection($inventories, true));
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreInventoryRequest $request): JsonResponse
    {
        $inventoryDTO = $request->toDTO();
        $this->validarLookups($inventoryDTO);
        $inventory = $this->inventoryService->create($inventoryDTO);
        return $this->showOne(new InventoryResource($inventory));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(UpdateInventoryRequest $request, int $id): JsonResponse
    {
        $inventoryDTO = $request->toDTO();
        if ($id !== $inventoryDTO->id) {
            throw new CustomErrorException(Message::INVALID_ID_PARAMETER_WITH_ID_BODY, Response::HTTP_BAD_REQUEST);
        }
        $this->validarLookups($inventoryDTO);
        $inventory = $this->inventoryService->update($id, $inventoryDTO);
        return $this->showOne(new InventoryResource($inventory));
    }

    /**
     * @throws CustomErrorException
     */
    public function updateImage(UpdateImageInventoryRequest $request, int $id): JsonResponse
    {
        $dto = $request->toDTO();
        $this->inventoryService->updateImage($id, $dto);
        return $this->noContentResponse();
    }

    public function show(int $id): JsonResponse
    {
        $inventory = $this->inventoryService->findById($id);
        return $this->showOne(new InventoryResource($inventory));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->inventoryService->delete($id);
        return $this->noContentResponse();
    }

    /**
     * @throws CustomErrorException
     */
    public function updateStock(UpdateStockInventoryRequest $request, int $id): JsonResponse
    {
        $dto = $request->toDTO();
        $this->inventoryService->updateStock($id, $dto);
        return $this->noContentResponse();
    }

    public function findAllCoffee(): JsonResponse
    {
        $officeId = auth()->user()->office_id;
        $inventories = $this->inventoryService->findAllCoffee($officeId);
        return $this->showAll(new InventoryCollection($inventories));
    }

    public function deleteImage(int $id): JsonResponse
    {
        $this->inventoryService->deleteImage($id);
        return $this->noContentResponse();
    }

    /**
     * @return void
     */
    private function validarLookups(InventoryDTO $inventoryDTO)
    {
        $this->lookupService->validateLookup($inventoryDTO->type_id, TypeLookup::INVENTORY_TYPE,
            'Tipo de inventario no válido.');
        $this->lookupService->validateLookup($inventoryDTO->unit_id, TypeLookup::UNIT_TYPE,
            'Unidad de medida no válido.');
    }
}
