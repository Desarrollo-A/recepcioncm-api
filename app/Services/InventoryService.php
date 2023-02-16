<?php

namespace App\Services;

use App\Contracts\Repositories\InventoryHistoryRepositoryInterface;
use App\Contracts\Repositories\InventoryRepositoryInterface;
use App\Contracts\Repositories\LookupRepositoryInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Path;
use App\Helpers\Enum\QueryParam;
use App\Helpers\File;
use App\Helpers\Validation;
use App\Models\Dto\InventoryDTO;
use App\Models\Dto\InventoryHistoryDTO;
use App\Models\Enums\Lookups\InventoryTypeLookup;
use App\Models\Enums\TypeLookup;
use App\Models\Inventory;
use App\Models\InventoryRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class InventoryService extends BaseService implements InventoryServiceInterface
{
    protected $entityRepository;
    protected $lookupRepository;
    protected $inventoryHistoryRepository;

    public function __construct(InventoryRepositoryInterface $inventoryRepository,
                                LookupRepositoryInterface $lookupRepository,
                                InventoryHistoryRepositoryInterface $inventoryHistoryRepository)
    {
        $this->entityRepository = $inventoryRepository;
        $this->lookupRepository = $lookupRepository;
        $this->inventoryHistoryRepository = $inventoryHistoryRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function create(InventoryDTO $dto): Inventory
    {
        return $this->entityRepository->create($dto->toArray());
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllPaginatedOffice(Request $request, User $user, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->entityRepository->findAllPaginatedOffice($user, $filters, $perPage, $sort, $columns);
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $id, InventoryDTO $dto): Inventory
    {
        return $this->entityRepository->update($id, $dto->toArray(['name', 'description', 'trademark', 'minimum_stock',
            'type_id', 'unit_id', 'status', 'meeting']));
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    public function updateStock(int $id, InventoryDTO $dto)
    {
        $inventory = $this->entityRepository->findById($id);
        $oldStock = $dto->stock;
        $dto->stock = $inventory->stock + $oldStock;
        if ($dto->stock < 0) {
            throw new CustomErrorException('El stock no puede quedar negativo', Response::HTTP_BAD_REQUEST);
        }

        $this->entityRepository->update($id, $dto->toArray(['stock']));

        $inventoryHistoryDTO = new InventoryHistoryDTO([
            'inventory_id' => $id,
            'quantity' => $oldStock,
            'cost' => $dto->cost
        ]);
        $this->inventoryHistoryRepository->create($inventoryHistoryDTO->toArray(['inventory_id', 'quantity', 'cost']));
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    public function updateImage(int $id, InventoryDTO $dto)
    {
        $inventory = $this->entityRepository->findById($id);

        $dto->image = File::uploadImage($dto->image_file, Path::INVENTORY_IMAGES, File::INVENTORY_HEIGHT_IMAGE);

        if ($inventory->image !== Inventory::IMAGE_DEFAULT) {
            File::deleteFile($inventory->image, Path::INVENTORY_IMAGES);
        }

        $this->entityRepository->update($id, $dto->toArray(['image']));
    }

    public function findAllCoffee(int $officeId): Collection
    {
        $snackTypeId = $this->lookupRepository->findByCodeAndType(InventoryTypeLookup::code(InventoryTypeLookup::COFFEE),
            TypeLookup::INVENTORY_TYPE)->id;
        return $this->entityRepository->findAllSnacks($snackTypeId, $officeId);
    }

    /**
     * @return void
     * @throws CustomErrorException
     */
    public function restoreStockAfterInventoryRequestDeleted(InventoryRequest $inventoryRequest)
    {
        if (!is_null($inventoryRequest->quantity)) {
            $this->entityRepository->incrementStock($inventoryRequest->inventory_id, $inventoryRequest->quantity);
        }
    }

    /**
     * @return void
     */
    public function restoreStockAfterInventoriesRequestDeleted(Collection $inventoriesRequest)
    {
        $inventoriesRequest->each( function ($snack) {
            if (!is_null($snack->quantity)) {
                $this->entityRepository->incrementStock($snack->id, $snack->quantity);
            }
        });
    }

    /**
     * @throws CustomErrorException
     * @return void
     */
    public function deleteImage(int $id)
    {
        $inventory = $this->entityRepository->findById($id);
        if ($inventory->image === Inventory::IMAGE_DEFAULT) {
            throw new CustomErrorException('No se puede eliminar la imagen por defecto', 400);
        }

        File::deleteFile($inventory->image, Path::INVENTORY_IMAGES);
        $dto = new InventoryDTO(['image' => Inventory::IMAGE_DEFAULT]);
        $this->entityRepository->update($id, $dto->toArray(['image']));
    }

    /**
     * @throws CustomErrorException
     */
    public function updateCode(Inventory $inventory)
    {
        $inventoryDTO = new InventoryDTO(['code' => Inventory::INITIAL_CODE.$inventory->id]);
        $this->entityRepository->update($inventory->id, $inventoryDTO->toArray(['code']));
    }
}