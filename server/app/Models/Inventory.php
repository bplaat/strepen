<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inventory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'price'
    ];

    // A inventory belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A inventory belongs to many products
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
        if (strlen($query) == 0) return $collection;
        return $collection->filter(function ($inventory) use ($query) {
            return Str::contains(strtolower($inventory->name), strtolower($query));
        });
    }
}
