<?php

namespace App\Repositories;

use App\Contracts\Repositories\OfficeRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Office;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class OfficeRepository extends BaseRepository implements OfficeRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|Office
     */
    protected $entity;

    public function __construct(Office $office)
    {
        $this->entity = $office;
    }

    public function findByName(string $name): Office
    {
        return $this->entity->where('name', $name)->firstOrFail();
    }

    public function getOfficeByStateWithDriver(int $stateId): Collection
    {
        return $this->entity
            ->where('state_id', $stateId)
            ->whereIn('id', function($query){
                return $query->selectRaw('DISTINCT(office_id)')
                    ->from('drivers');
            })
            ->orderBy('name', 'ASC')
            ->get();
    }

}