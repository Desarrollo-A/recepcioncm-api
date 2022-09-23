<?php

namespace App\Models;

class InventoryHistory
{
    protected $table = 'inventory_history';

    public $timestamps = false;

    protected $fillable = ['inventory_id', 'quantity', 'cost'];
}