<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    // A transaction can be a normal transaction or a deposit
    const TYPE_TRANSACTION = 0;
    const TYPE_DEPOSIT = 1;

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
    public static function search($searchQuery)
    {
        return static::where('deleted', false)
            ->where('name', 'LIKE', '%' . $searchQuery . '%');
    }

    // Search collection by a query
    public static function searchCollection($collection, $searchQuery)
    {
        if (strlen($searchQuery) == 0) {
            return $collection->filter(function ($transaction) {
                return !$transaction->deleted;
            });
        }
        return $collection->filter(function ($transaction) use ($searchQuery) {
            return !$transaction->deleted && (
                Str::contains(strtolower($transaction->name), strtolower($searchQuery))
            );
        });
    }
}
