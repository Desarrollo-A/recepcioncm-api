<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InventoryRequest extends Pivot
{
    protected $fillable = ['request_id', 'inventory_id', 'quantity', 'applied', 'created_at', 'updated_at'];

    protected $casts = [
        'request_id' => 'integer',
        'inventory_id' => 'integer',
        'quantity' => 'integer',
        'applied' => 'boolean'
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}

