<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryProduct extends Model
{
    protected $fillable = [
        'inventory_id',
        'product_id',
        'amount'
    ];

    // A inventory product belongs to a products
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
