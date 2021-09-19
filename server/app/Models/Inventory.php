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
    public static function search($query, $searchQuery)
    {
        return $query->where('deleted', false)
            ->where('name', 'LIKE', '%' . $searchQuery . '%');
    }
}
