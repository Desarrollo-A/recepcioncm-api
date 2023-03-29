<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Package extends Model
{
    protected $fillable = ['pickup_address_id', 'arrival_address_id', 'name_receive', 'email_receive', 'request_id',
        'office_id', 'auth_code', 'is_urgent', 'is_heavy_shipping'];

    protected $casts = [
        'id' => 'integer',
        'pickup_address_id' => 'integer',
        'arrival_address_id' => 'integer',
        'request_id' => 'integer',
        'office_id' => 'integer',
        'is_urgent' => 'boolean',
        'is_heavy_shipping' => 'boolean',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function pickupAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'pickup_address_id');
    }

    public function arrivalAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'arrival_address_id');
    }

    public function driverPackageSchedule(): HasOne
    {
        return $this->hasOne(DriverPackageSchedule::class);
    }

    public function deliveredPackage(): HasOne
    {
        return $this->hasOne(DeliveredPackage::class);
    }

    public function proposalPackage(): HasOne
    {
        return $this->hasOne(ProposalPackage::class);
    }

    public function heavyShippments(): HasMany
    {
        return $this->hasMany(HeavyShipment::class);
    }

    public function detailExternalParcel(): HasOne
    {
        return $this->hasOne(DetailExternalParcel::class);
    }
}
