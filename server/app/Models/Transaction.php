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

    // Turn model to api data
    public function forApi($user)
    {
        if ($user == null || $user->role != User::ROLE_ADMIN) {
            unset($this->updated_at);
        }

        $this->user->forApi($user);

        if ($this->type == static::TYPE_TRANSACTION) {
            $this->type = 'transaction';

            foreach ($this->products as $product) {
                $product->forApi($user);
                $product->amount = $product->pivot->amount;
                unset($product->pivot);
            }
        }

        if ($this->type == static::TYPE_DEPOSIT) $this->type = 'deposit';
        if ($this->type == static::TYPE_FOOD) $this->type = 'food';
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
