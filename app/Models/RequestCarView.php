<?php

namespace App\Models;

use App\Models\Contracts\ScopeFilterInterface;
use App\Models\Enums\NameRole;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RequestCarView extends Model implements ScopeFilterInterface
{
    use Sortable;
    
    protected $table = 'request_car_view';

    public $allowedSorts = ['request_id', 'code', 'title', 'start_date', 'end_date', 'status_name', 'full_name'];

    protected $casts = [
        'request_id'    =>  'integer',
        'start_date'    =>  'datetime',
        'end_date'      =>  'datetime',
        'office_id'     =>  'integer',
        'user_id'       =>  'integer'
    ];

    public function scopeFilter(Builder $query, array $params = []): Builder
    {
        if(empty($params)){
            return $query;
        }

        if(isset($params['code']) && trim($params['code']) !== ''){
            $query->orWhere('code', 'LIKE', "%${params['code']}%");
        }
        
        if(isset($params['title']) && trim($params['title']) !== ''){
            $query->orWhere('title', 'LIKE', "%${params['title']}%");
        }
        
        if(isset($params['status_name']) && trim($params['status_name']) !== ''){
            $query->orWhere('status_name', 'LIKE', "%${params['status_name']}%");
        }
        
        if(isset($params['full_name']) && trim($params['full_name']) !== ''){
            $query->orWhere('full_name', 'LIKE', "%${params['full_name']}%");
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
}
