<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\InputOutputInventoryViewServiceInterface;
use App\Core\BaseApiController;
use App\Http\Resources\InputOutputInventory\InputOutputInventoryCollection;
use App\Models\Enums\NameRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InputOutputInventoryViewController extends BaseApiController
{
    private $inputOutputInventoryViewService;

    public function __construct(InputOutputInventoryViewServiceInterface $inputOutputInventoryViewService)
    {
        $this->middleware('role.permission:'.NameRole::RECEPCIONIST);

        $this->inputOutputInventoryViewService = $inputOutputInventoryViewService;
    }

    public function findAllPaginated(Request $request): JsonResponse
    {
        $data = $this->inputOutputInventoryViewService->findAllRoomsPaginated($request, auth()->user()->office_id);
        return $this->showAll(new InputOutputInventoryCollection($data, true));
    }

    public function getReportPdf(Request $request)
    {
        return $this->inputOutputInventoryViewService->reportInputOutputPdf($request, auth()->user()->office_id);
    }
}
