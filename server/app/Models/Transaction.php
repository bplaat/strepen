<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    // A transaction can be a normal transaction, a deposit or a payment
    public const TYPE_TRANSACTION = 0;

    public const TYPE_DEPOSIT = 1;

    public const TYPE_PAYMENT = 2;

    protected $hidden = [
        'deleted_at',
    ];

    protected $casts = [
        'price' => 'double',
    ];

    // A transaction belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // A transaction belongs to many products
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'transaction_product')->withPivot('amount')->withTimestamps()->withTrashed();
    }

    // Search by a query
    public static function search(Builder $query, string $searchQuery): Builder
    {
        if ($searchQuery != '') {
            return $query->where(fn ($query) => $query->where('name', 'LIKE', '%'.$searchQuery.'%')
                ->orWhere('created_at', 'LIKE', '%'.$searchQuery.'%'));
        }

        return $query;
    }
}
