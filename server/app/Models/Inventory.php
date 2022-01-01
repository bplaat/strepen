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

    // Convert inventory to API data
    public function toApiData($forUser = null, $includes = [])
    {
        $data = new \stdClass();
        $data->id = $this->id;
        $data->name = $this->name;
        $data->price = $this->price;
        $data->created_at = $this->created_at;

        if ($forUser != null && ($forUser->role == User::ROLE_MANAGER || $forUser->role == User::ROLE_ADMIN)) {
            $data->updated_at = $this->updated_at;
        }

        if (in_array('user', $includes)) {
            $data->user = $this->user->toApiData($forUser);
        }

        if (in_array('products', $includes)) {
            $data->products = $this->products->map(fn ($product) => $product->toApiData($forUser));
        }

        return $data;
    }
}
