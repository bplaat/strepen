<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    // A transaction can be a normal transaction, a deposit or a food transaction
    const TYPE_TRANSACTION = 0;
    const TYPE_DEPOSIT = 1;
    const TYPE_FOOD = 2;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'price'
    ];

    // A transaction belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A transaction belongs to many products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'transaction_product')->withPivot('amount')->withTimestamps();
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('deleted', false)
            ->where('name', 'LIKE', '%' . $searchQuery . '%');
    }
}
