<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\MovementRequestServiceInterface;
use App\Contracts\Services\PerDiemServiceInterface;
use App\Core\BaseApiController;
use App\Exceptions\CustomErrorException;
use App\Http\Requests\PerDiem\StorePerDiemRequest;
use App\Http\Requests\PerDiem\UpdateSpentPerDiemRequest;
use App\Http\Requests\PerDiem\UploadBillFilesRequest;
use App\Http\Resources\PerDiem\PerDiemResource;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PerDiemController extends BaseApiController
{
    private $perDiemService;
    private $movementRequestService;

    public function __construct(
        PerDiemServiceInterface $perDiemService,
        MovementRequestServiceInterface $movementRequestService
    )
    {
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('store');
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('updateSpent', 'uploadBillFiles');

        $this->perDiemService = $perDiemService;
        $this->movementRequestService = $movementRequestService;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StorePerDiemRequest $request): JsonResponse
    {
        $perDiem = $this->perDiemService->store($request->toDTO());
        $this->movementRequestService->create($perDiem->request_id, auth()->id(), 'Se agrega primera información de viáticos');
        return $this->showOne(new PerDiemResource($perDiem));
    }

    /**
     * @throws CustomErrorException
     */
    public function updateSpent(int $id, UpdateSpentPerDiemRequest $request): JsonResponse
    {
        $perDiem = $this->perDiemService->update($id, $request->toDTO());
        $this->movementRequestService->create($perDiem->request_id, auth()->id(), 'Se agrega costo total del viaje a la solicitud');
        return $this->showOne(new PerDiemResource($perDiem));
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadBillFiles(int $id, UploadBillFilesRequest $request): Response
    {
        $dto = $request->toDTO();
        $this->perDiemService->uploadBillFiles($id, $dto);
        $this->movementRequestService->create($id, auth()->id(), 'Se cargan facturas a la solicitud');
        return $this->noContentResponse();
    }
}
