<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface InputOutputInventoryViewServiceInterface extends BaseServiceInterface
{
    public function findAllRoomsPaginated(Request $request, int $officeId, array $columns = ['*']): LengthAwarePaginator;

    public function reportInputOutputPdf(Request $request, int $officeId);
}