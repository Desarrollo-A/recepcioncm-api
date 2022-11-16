<?php

namespace App\Models;

use App\Models\Contracts\ScopeFilterInterface;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PhpParser\Node\Expr\FuncCall;

class Driver extends Model implements ScopeFilterInterface
{
    use Sortable;

    public $allowedSorts = ['id', 'no_employee', 'full_name', 'email', 'personal_phone'];

    protected $fillable = ['no_employee', 'full_name', 'email', 'personal_phone', 'office_phone', 'office_id',
        'status_id'];

    protected $casts = [
        'id' => 'integer',
        'office_id' => 'integer',
        'status_id' => 'integer'
    ];

    public function scopeFilter(Builder $query, array $params = []): Builder
    {
        if(empty($params)){
            return $query;
        }

        if (isset($params['no_employee']) && trim($params['no_employee'])) {
            $query->orWhere('no_employee', 'LIKE', "%${params['no_employee']}%");
        }

        if (isset($params['full_name']) && trim($params['full_name'])) {
            $query->orWhere('full_name', 'LIKE', "%${params['full_name']}%");
        }

        if (isset($params['email']) && trim($params['email'])) {
            $query->orWhere('email', 'LIKE', "%${params['email']}%");
        }

        if (isset($params['personal_phone']) && trim($params['personal_phone'])) {
            $query->orWhere('personal_phone', 'LIKE', "%${params['personal_phone']}%");
        }

        return $query;
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id', 'id');
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id', 'id');//Primeramente se pone llave foranea.
    }
}
