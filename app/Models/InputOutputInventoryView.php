<?php

namespace App\Models;

use App\Models\Contracts\ScopeFilterInterface;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InputOutputInventoryView extends Model implements ScopeFilterInterface
{
    use Sortable;

    protected $table = 'input_output_inventory_view';

    public $allowedSorts = ['code', 'name', 'type', 'move_date'];

    protected $casts = [
        'sum_quantity' => 'double',
        'sum_cost' => 'double',
        'office_id' => 'integer',
        'move_date' => 'date',
        'type_id' => 'integer'
    ];

    public function scopeFilter(Builder $query, array $params = []): Builder
    {
        if (empty($params)) {
            return $query;
        }

        if (isset($params['code']) && trim($params['code']) !== '') {
            $query->orWhere('code', 'LIKE', "%${params['code']}%");
        }
        if (isset($params['name']) && trim($params['name']) !== '') {
            $query->orWhere('name', 'LIKE', "%${params['name']}%");
        }
        if (isset($params['type']) && trim($params['type']) !== '') {
            $query->orWhere('type', 'LIKE', "%${params['type']}%");
        }

        return $query;
    }

    public function scopeFilterReport(Builder $query, array $params = []): Builder
    {
        if (empty($params)) {
            return $query;
        }

        if (isset($params['start_date'])) {
            $query->where('move_date', '>=', $params['start_date']);
        }
        if (isset($params['end_date'])) {
            $query->where('move_date', '<=', $params['end_date']);
        }

        return $query;
    }
}
