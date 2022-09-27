<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\InventoryRequestServiceInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\Message;
use App\Http\Requests\InventoryRequest\StoreInventoryRequestRequest;
use App\Http\Requests\InventoryRequest\UpdateInventoryRequestRequest;
use App\Http\Resources\InventoryRequest\InventoryRequestResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class InventoryRequestController extends BaseApiController
{
    private $inventoryRequestService;
    private $inventoryService;

    public function __construct(InventoryRequestServiceInterface $inventoryRequestService,
                                InventoryServiceInterface $inventoryService)
    {
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('store', 'update', 'delete');
        $this->inventoryRequestService = $inventoryRequestService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StoreInventoryRequestRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $officeId = auth()->user()->office_id;
        $inventoryRequest = $this->inventoryRequestService->createSnack($dto, $officeId);
        return $this->showOne(new InventoryRequestResource($inventoryRequest));
    }

    /**
     * @throws CustomErrorException
     */
    public function update(int $requestId, int $inventoryId, UpdateInventoryRequestRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        if ($requestId !== $dto->request_id || $inventoryId !== $dto->inventory_id) {
            throw new CustomErrorException(Message::INVALID_ID_PARAMETER_WITH_ID_BODY, Response::HTTP_BAD_REQUEST);
        }

        $officeId = auth()->user()->office_id;
        $this->inventoryRequestService->updateSnack($requestId, $inventoryId, $dto, $officeId);
        return $this->noContentResponse();
    }

    public function delete(int $requestId, int $inventoryId): JsonResponse
    {
        $inventoryRequest = $this->inventoryRequestService->deleteSnack($requestId, $inventoryId);
        $this->inventoryService->restoreStockAfterInventoryRequestDeleted($inventoryRequest);
        return $this->noContentResponse();
    }
}
