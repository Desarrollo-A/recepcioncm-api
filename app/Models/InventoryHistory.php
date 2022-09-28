<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    protected $table = 'inventory_history';

    public $timestamps = false;

    protected $fillable = ['inventory_id', 'quantity', 'cost'];
}