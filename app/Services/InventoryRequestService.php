<?php

namespace App\Services;

use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\InventoryRequestRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Services\InventoryRequestServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Models\Dto\InventoryDTO;
use App\Models\Dto\InventoryRequestDTO;
use App\Models\Enums\Lookups\InventoryTypeLookup;
use App\Models\Enums\Lookups\StatusRequestLookup;
use App\Models\Enums\TypeLookup;
use App\Models\InventoryRequest;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

class InventoryRequestService extends BaseService implements InventoryRequestServiceInterface
{
    protected $entityRepository;
    protected $lookupRepository;
    protected $requestRepository;
    protected $inventoryRepository;

    public function __construct(InventoryRequestRepositoryInterface $inventoryRequestRepository,
                                LookupRepositoryInterface $lookupRepository,
                                RequestRepositoryInterface $requestRepository,
                                InventoryRepositoryInterface $inventoryRepository)
    {
        $this->entityRepository = $inventoryRequestRepository;
        $this->lookupRepository = $lookupRepository;
        $this->requestRepository = $requestRepository;
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(InventoryRequestDTO $dto): InventoryRequest
    {
        return $this->entityRepository->create($dto->toArray());
    }

    /**
     * @throws CustomErrorException
     */
    public function createSnack(InventoryRequestDTO $dto, int $officeId): InventoryRequest
    {
        $this->validationInsertOrUpdate($dto, $officeId);

        return $this->entityRepository->create($dto->toArray(['request_id', 'inventory_id', 'quantity', 'applied']));
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    public function updateSnack(int $requestId, int $inventoryId, InventoryRequestDTO $dto, int $officeId)
    {
        $this->validationInsertOrUpdate($dto, $officeId);

        $oldStock = $this->inventoryRepository->findById($inventoryId, ['stock'])->stock;
        $oldQuantity = $this->entityRepository->findByRequestIdAndInventoryId($requestId, $inventoryId, ['quantity'])->quantity;
        $this->entityRepository->updateInventoryRequest($requestId, $inventoryId, $dto->toArray(['quantity']));
        $newStock = ($oldStock + $oldQuantity) - $dto->quantity;
        $dto = new InventoryDTO(['stock' => $newStock]);
        $this->inventoryRepository->update($inventoryId, $dto->toArray('stock'));
    }

    public function deleteSnack(int $requestId, int $inventoryId): InventoryRequest
    {
        $inventoryRequest = $this->entityRepository->findByRequestIdAndInventoryId($requestId, $inventoryId);
        $this->entityRepository->deleteInventory($requestId, $inventoryId);
        return $inventoryRequest;
    }

    public function deleteSnacks(int $requestId): Collection
    {
        $inventoriesRequest = $this->entityRepository->getByRequestIdAndQuantity($requestId);
        $this->entityRepository->deleteInventories($requestId);
        return $inventoriesRequest;
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    private function validationInsertOrUpdate(InventoryRequestDTO $dto, int $officeId)
    {
        $newStatusId = $this->lookupRepository->findByCodeAndType(StatusRequestLookup::code(StatusRequestLookup::APPROVED),
            TypeLookup::STATUS_REQUEST)->id;
        $request = $this->requestRepository->findById($dto->request_id);

        if ($request->status_id !== $newStatusId) {
            throw new CustomErrorException('La solicitud debe estar en estatus ' . StatusRequestLookup::code(StatusRequestLookup::APPROVED),
                Response::HTTP_BAD_REQUEST);
        }

        $snackTypeId = $this->lookupRepository->findByCodeAndType(InventoryTypeLookup::code(InventoryTypeLookup::COFFEE),
            TypeLookup::INVENTORY_TYPE)->id;

        $inventories = $this->inventoryRepository->findAllByType($snackTypeId, $officeId);

        self::validateInventoryAsSnack($inventories, $dto, $snackTypeId);
        self::validateStockSnack($inventories, $dto);
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    private function validateStockSnack(Collection $snacks, InventoryRequestDTO $dto)
    {
        $snack = $snacks->first(function ($inventory) use ($dto) {
            return $dto->inventory_id === $inventory->id;
        });

        if (!is_null($snack->meeting) && !is_null($dto->quantity)) {
            throw new CustomErrorException("Snack no debe tener cantidad a descontar",
                Response::HTTP_BAD_REQUEST);
        }
        if (is_null($snack->meeting) && is_null($dto->quantity)) {
            throw new CustomErrorException("Snack debe tener cantidad a descontar",
                Response::HTTP_BAD_REQUEST);
        }
        if (($snack->stock - $dto->quantity) < 0) {
            throw new CustomErrorException("Snack no debe quedar stock negativo",
                Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    private function validateInventoryAsSnack(Collection $snacks, InventoryRequestDTO $dto, int $snackTypeId)
    {
        $snack = $snacks->first(function ($inventory) use ($dto, $snackTypeId) {
            return $dto->inventory_id === $inventory->id
            && $inventory->type_id === $snackTypeId;
        });

        if (is_null($snack)) {
            throw new CustomErrorException("Snack no encontrado", Response::HTTP_BAD_REQUEST);
        }
    }
}