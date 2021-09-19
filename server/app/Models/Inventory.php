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
    public static function search($searchQuery)
    {
        return static::where('deleted', false)
            ->where('name', 'LIKE', '%' . $searchQuery . '%');
    }

    // Search collection by a query
    public static function searchCollection($collection, $searchQuery)
    {
        if (strlen($searchQuery) == 0) {
            return $collection->filter(function ($inventory) {
                return !$inventory->deleted;
            });
        }
        return $collection->filter(function ($inventory) use ($searchQuery) {
            return !$inventory->deleted && (
                Str::contains(strtolower($inventory->name), strtolower($searchQuery))
            );
        });
    }
}
