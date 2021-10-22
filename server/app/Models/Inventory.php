<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inventory extends Model
{
    protected $hidden = [
        'deleted'
    ];

    protected $casts = [
        'price' => 'double',
        'active' => 'boolean',
        'deleted' => 'boolean'
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

    // Turn model to api data
    public function forApi($user)
    {
        $this->user->forApi($user);

        foreach ($this->products as $product) {
            $product->forApi($user);
            $product->amount = $product->pivot->amount;
            unset($product->pivot);
        }
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('deleted', false)
            ->where(function ($query) use ($searchQuery) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
            });
    }
}
