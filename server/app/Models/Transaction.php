<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsToMany(Product::class)->withPivot('amount')->withTimestamps();
    }

    // Search by a query
    public static function search($query)
    {
        return static::where('name', 'LIKE', '%' . $query . '%')->get();
    }

    // Search collection by a query
    public static function searchCollection($collection, $query)
    {
        return $collection->filter(function ($transaction) use ($query) {
            return Str::contains(strtolower($transaction->name), strtolower($query));
        });
    }
}
