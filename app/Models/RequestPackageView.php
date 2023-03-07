<?php

namespace App\Models;

use App\Models\Contracts\ScopeFilterInterface;
use App\Models\Enums\NameRole;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RequestPackageView extends Model implements ScopeFilterInterface
{
    use Sortable;

    protected $table = 'request_package_view';

    public $allowedSorts = ['request_id', 'code', 'title', 'start_date', 'status_name', 'full_name', 'state_pickup', 'state_arrival'];

    protected $casts = [
        'request_id' => 'integer',
        'start_date' => 'datetime',
        'end_date'  =>  'datetime',
        'office_id' => 'integer',
        'package_id' => 'integer',
        'driver_id' => 'integer'
    ];

    public function scopeFilter(Builder $query, array $params = []): Builder
    {
        if (empty($params)) {
            return $query;
        }

        if (isset($params['code']) && trim($params['code']) !== '') {
            $query->orWhere('code', 'LIKE', "%${params['code']}%");
        }
        if (isset($params['title']) && trim($params['title']) !== '') {
            $query->orWhere('title', 'LIKE', "%${params['title']}%");
        }
        if (isset($params['username']) && trim($params['username']) !== '') {
            $query->orWhere('full_name', 'LIKE', "%${params['username']}%");
        }
        if (isset($params['status_name']) && trim($params['status_name']) !== '') {
            $query->orWhere('status_name', 'LIKE', "%${params['status_name']}%");
        }
        if (isset($params['state_pickup']) && trim($params['state_pickup']) !== '') {
            $query->orWhere('state_pickup', 'LIKE', "%${params['state_pickup']}%");
        }
        if (isset($params['state_arrival']) && trim($params['state_arrival']) !== '') {
            $query->orWhere('state_arrival', 'LIKE', "%${params['state_arrival']}%");
        }

        return $query;
    }

    public function scopeFilterOfficeOrUser(Builder $query, User $user): Builder
    {
        if ($user->role->name === NameRole::RECEPCIONIST) {
            $query->where('office_id', $user->office_id);
        } else if ($user->role->name === NameRole::APPLICANT) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public function scopeFilterReport(Builder $query, array $params = []): Builder
    {
        if (empty($params)) {
            return $query;
        }

        if (isset($params['start_date'])) {
            $query->orWhere('end_date', '>=', $params['start_date']);
        }
        if (isset($params['end_date'])) {
            $query->orWhere('end_date', '<=', $params['end_date']);
        }

        return $query;
    }
}
