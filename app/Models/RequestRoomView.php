<?php

namespace App\Models;

use App\Models\Contracts\ScopeFilterInterface;
use App\Models\Enums\NameRole;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RequestRoomView extends Model implements ScopeFilterInterface
{
    use Sortable;

    protected $table = 'request_room_view';

    public $allowedSorts = ['id', 'code', 'title', 'start_date', 'full_name', 'status_name', 'room_name', 'level_meeting'];

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
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
        if (isset($params['date'])) {
            $query->orWhereDate('start_date', $params['date']);
        }
        if (isset($params['start_time'])) {
            $query->orWhereTime('start_date', $params['start_time']);
        }
        if (isset($params['end_time'])) {
            $query->orWhereTime('end_date', $params['end_time']);
        }
        if (isset($params['room_name']) && trim($params['room_name']) !== '') {
            $query->orWhere('room_name', 'LIKE', "%${params['room_name']}%");
        }
        if (isset($params['level_meeting']) && trim($params['level_meeting']) !== '') {
            $query->orWhere('level_meeting', 'LIKE', "%${params['level_meeting']}%");
        }
        if (isset($params['status_name']) && trim($params['status_name']) !== '') {
            $query->orWhere('status_name', 'LIKE', "%${params['status_name']}%");
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
