<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
    ];

    protected $casts = [
        'price' => 'double',
    ];

    // A inventory belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // A inventory belongs to many products
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('amount')->withTimestamps()->withTrashed();
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
