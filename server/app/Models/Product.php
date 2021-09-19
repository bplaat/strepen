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
    public static function search($searchQuery)
    {
        return static::where('deleted', false)
            ->where(function ($query) use ($searchQuery) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('description', 'LIKE', '%' . $searchQuery . '%');
            });
    }

    // Search collection by a query
    public static function searchCollection($collection, $searchQuery)
    {
        if (strlen($searchQuery) == 0) {
            return $collection->filter(function ($product) {
                return !$product->deleted;
            });
        }
        return $collection->filter(function ($product) use ($searchQuery) {
            return !$product->deleted && (
                Str::contains(strtolower($product->name), strtolower($searchQuery)) ||
                Str::contains(strtolower($product->description), strtolower($searchQuery))
            );
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
