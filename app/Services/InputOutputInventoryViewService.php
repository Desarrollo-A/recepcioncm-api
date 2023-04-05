<?php

namespace App\Services;

use App\Contracts\Repositories\InputOutputInventoryViewRepositoryInterface;
use App\Contracts\Services\InputOutputInventoryViewServiceInterface;
use App\Core\BaseService;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\QueryParam;
use App\Helpers\File;
use App\Helpers\Validation;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        return File::generatePDF('pdf.reports.input-output-inventory', $data,'entradas_salidas_inventario');
    }

    /**
     * @throws CustomErrorException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     * @throws InvalidArgumentException
     * @throws IOException
     */
    public function reportInputOutputExcel(Request $request, int $officeId): StreamedResponse
    {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $data = $this->entityRepository->getDataReport($filters, $officeId)->map(function ($item) {
            return collect([
                'Clave' => $item->code,
                'Nombre' => $item->name,
                'Tipo' => $item->type,
                'Cantidad' => number_format($item->sum_quantity),
                'Costo' => $item->sum_cost ? '$'.number_format($item->sum_cost) : 'No aplica',
                'Fecha movimiento' => $item->move_date->format('d-m-Y')
            ]);
        });

        return File::generateExcel($data,'entradas_salidas_inventario');
    }
}