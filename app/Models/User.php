<?php

namespace App\Models;

use App\Models\Enums\NameRole;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, Sortable;

    const RESTORE_PASSWORD_LENGTH = 15;

    public $allowedSorts = ['id', 'no_employee', 'full_name', 'email', 'personal_phone', 'position', 'area'];

    protected $fillable = [
        'no_employee',
        'full_name',
        'email',
        'password',
        'personal_phone',
        'office_phone',
        'position',
        'area',
        'role_id',
        'status_id',
        'office_id',
        'department_manager_id'
    ];

    protected $casts = [
        'id' => 'integer',
        'status_id' => 'integer',
        'role_id' => 'integer',
        'office_id' => 'integer',
        'department_manager_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeFilter(Builder $query, array $params = []): Builder
    {
        if (empty($params)) {
            return $query;
        }

        if (isset($params['no_employee']) && trim($params['no_employee']) !== '') {
            $query->orWhere('no_employee', 'LIKE', "%{$params['no_employee']}%");
        }
        if (isset($params['full_name']) && trim($params['full_name']) !== '') {
            $query->orWhere('full_name', 'LIKE', "%{$params['full_name']}%");
        }
        if (isset($params['email']) && trim($params['email']) !== '') {
            $query->orWhere('email', 'LIKE', "%{$params['email']}%");
        }
        if (isset($params['personal_phone']) && trim($params['personal_phone']) !== '') {
            $query->orWhere('personal_phone', 'LIKE', "%{$params['personal_phone']}%");
        }
        if (isset($params['position']) && trim($params['position']) !== '') {
            $query->orWhere('position', 'LIKE', "%{$params['position']}%");
        }
        if (isset($params['area']) && trim($params['area']) !== '') {
            $query->orWhere('area', 'LIKE', "%{$params['area']}%");
        }

        return $query;
    }

    public function scopeWithoutUser(Builder $query, int $userId): Builder
    {
        $query->where('id', '!=', $userId);

        return $query;
    }

    public function scopeDriverUser(Builder $query): Builder
    {
        $query->whereHas('role', function (Builder $query){
            $query->where('name', NameRole::DRIVER);
        });

        return $query;
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'status_id', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class)
            ->withTimestamps();
    }

    public function submenus(): BelongsToMany
    {
        return $this->belongsToMany(Submenu::class)->withTimestamps();
    }

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, 'car_driver', 'driver_id')
            ->withTimestamps();
    }

    public function officeManager(): HasOne
    {
        return $this->hasOne(OfficeManager::class, 'manager_id', 'id');
    }
}
