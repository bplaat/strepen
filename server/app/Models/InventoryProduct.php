<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryProduct extends Model
{
    protected $table = 'inventory_product';

    protected $fillable = [
        'inventory_id',
        'product_id',
        'amount'
    ];
}
