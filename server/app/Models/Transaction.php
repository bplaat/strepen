<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    // A transaction can be a normal transaction, a deposit or a food transaction
    public const TYPE_TRANSACTION = 0;
    public const TYPE_DEPOSIT = 1;
    public const TYPE_FOOD = 2;

    protected $hidden = [
        'deleted'
    ];

    protected $casts = [
        'price' => 'double',
        'active' => 'boolean',
        'deleted' => 'boolean'
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
            ->where(fn ($query) => $query->where('name', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%'));
    }

    // Convert transaction to API data
    public function toApiData($forUser = null, $includes = [])
    {
        $data = new \stdClass();
        $data->id = $this->id;

        if ($this->type == static::TYPE_TRANSACTION) {
            $data->type = 'transaction';
        }
        if ($this->type == static::TYPE_DEPOSIT) {
            $data->type = 'deposit';
        }
        if ($this->type == static::TYPE_FOOD) {
            $data->type = 'food';
        }

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
