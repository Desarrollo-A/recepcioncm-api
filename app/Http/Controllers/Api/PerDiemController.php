<?php

namespace App\Http\Controllers\Api;

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

    public function __construct(PerDiemServiceInterface $perDiemService)
    {
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST)
            ->only('store');
        $this->middleware('role.permission:'.NameRole::APPLICANT)
            ->only('updateSpent', 'uploadBillFiles');

        $this->perDiemService = $perDiemService;
    }

    /**
     * @throws CustomErrorException
     */
    public function store(StorePerDiemRequest $request): JsonResponse
    {
        $perDiem = $this->perDiemService->store($request->toDTO());
        return $this->showOne(new PerDiemResource($perDiem));
    }

    /**
     * @throws CustomErrorException
     */
    public function updateSpent(int $id, UpdateSpentPerDiemRequest $request): JsonResponse
    {
        $perDiem = $this->perDiemService->update($id, $request->toDTO());
        return $this->showOne(new PerDiemResource($perDiem));
    }

    /**
     * @throws CustomErrorException
     */
    public function uploadBillFiles(int $id, UploadBillFilesRequest $request): Response
    {
        $dto = $request->toDTO();
        $this->perDiemService->uploadBillFiles($id, $dto);
        return $this->noContentResponse();
    }
}
