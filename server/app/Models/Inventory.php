<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Inventory extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at'
    ];

    protected $casts = [
        'price' => 'double',
        'active' => 'boolean'
    ];

    protected $attributes = [
        'active' => true
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
        return $query->where(fn ($query) => $query->where('name', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%'));
    }
}
