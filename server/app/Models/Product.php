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

    protected $casts = [
        'active' => 'boolean'
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

        // Loop through all transactions and adjust amount
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
            ->orWhere('description', 'LIKE', '%' . $query . '%');
    }

    // Search collection by a query
    public static function searchCollection($collection, $query)
    {
        if (strlen($query) == 0) return $collection;
        return $collection->filter(function ($product) use ($query) {
            return Str::contains(strtolower($product->name), strtolower($query)) ||
                Str::contains(strtolower($product->description), strtolower($query));
        });
    }

    // Get amount chart data
    public function getAmountChart() {
        $amount = 0;
        $amountData = [];

        $inventories = $this->inventories->sortBy('created_at');
        foreach ($inventories as $inventory) {
            $amount += $inventory->pivot->amount;
            $amountData[] = [ $inventory->created_at->format('Y-m-d'), $amount ];
        }

        $transactions = $this->transactions->sortBy('created_at');
        foreach ($transactions as $transaction) {
            $amount -= $transaction->pivot->amount;
            $amountData[] = [ $transaction->created_at->format('Y-m-d'), $amount ];
        }

        usort($amountData, function ($a, $b) {
            return strcmp($a[0], $b[0]);
        });

        return $amountData;
    }
}
