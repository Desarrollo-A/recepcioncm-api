<?php

namespace App\Contracts\Services;

use App\Core\Contracts\BaseServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface InputOutputInventoryViewServiceInterface extends BaseServiceInterface
{
    public function findAllRoomsPaginated(Request $request, int $officeId, array $columns = ['*']): LengthAwarePaginator;

    public function reportInputOutputPdf(Request $request, int $officeId);

    public function reportInputOutputExcel(Request $request, int $officeId): StreamedResponse;
}