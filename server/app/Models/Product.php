<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'amount'
    ];

    // Generate a random image name
    public static function generateImageName($extension)
    {
        if ($extension == 'jpeg') $extension = 'jpg';
        return Str::random(32) . '.' . $extension;
    }

    // Recalculate product amount
    public function recalculateAmount()
    {
        // Refresh relationships
        unset($this->inventories);
        unset($this->transactions);

        // Recount amount
        $this->amount = 0;

        // Loop through all inventories and adjust amount
        $inventories = $this->inventories->sortBy('created_at');
        foreach ($inventories as $inventory) {
            $this->amount += $inventory->pivot->amount;
        }

        // Loop through all transactions and adjust balance
        $transactions = $this->transactions->sortBy('created_at');
        foreach ($transactions as $transaction) {
            $this->amount -= $transaction->pivot->amount;
        }
    }

    // A product belongs to many inventories
    public function inventories()
    {
        return $this->belongsToMany(Inventory::class)->withPivot('amount')->withTimestamps();
    }

    // A product belongs to many transactions
    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_product')->withPivot('amount')->withTimestamps();
    }

    // Search by a query
    public static function search($query)
    {
        return static::where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('description', 'LIKE', '%' . $query . '%')->get();
    }

    // Search collection by a query
    public static function searchCollection($collection, $query)
    {
        return $collection->filter(function ($product) use ($query) {
            return Str::contains(strtolower($product->name), strtolower($query)) ||
                Str::contains(strtolower($product->description), strtolower($query));
        });
    }
}
