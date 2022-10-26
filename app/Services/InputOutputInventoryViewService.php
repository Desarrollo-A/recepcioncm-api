<?php

namespace App\Services;

use App\Contracts\Repositories\InputOutputInventoryViewRepositoryInterface;
use App\Contracts\Services\InputOutputInventoryViewServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\QueryParam;
use App\Helpers\File;
use App\Helpers\Validation;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class InputOutputInventoryViewService extends BaseService implements InputOutputInventoryViewServiceInterface
{
    protected $entityRepository;

    public function __construct(InputOutputInventoryViewRepositoryInterface $inputOutputInventoryViewRepository)
    {
        $this->entityRepository = $inputOutputInventoryViewRepository;
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllRoomsPaginated(Request $request, int $officeId, array $columns = ['*']): LengthAwarePaginator
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->entityRepository->findAllInventoriesPaginated($filters, $perPage, $officeId, $sort, $columns);
    }

    /**
     * @throws CustomErrorException
     */
    public function reportInputOutputPdf(Request $request, int $officeId)
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $data = $this->entityRepository->getDataReport($filters, $officeId);
        return File::generatePDF('pdf.reports.input-output-inventory', array('items' => $data),
            'entradas_salidas_inventario.pdf');
    }
}