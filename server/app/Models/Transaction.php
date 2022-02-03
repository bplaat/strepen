<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    // A transaction can be a normal transaction, a deposit or a food transaction
    public const TYPE_TRANSACTION = 0;
    public const TYPE_DEPOSIT = 1;
    public const TYPE_FOOD = 2;

    protected $hidden = [
        'deleted_at'
    ];

    protected $casts = [
        'price' => 'double'
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
        return $query->where(fn ($query) => $query->where('name', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%'));
    }
}
