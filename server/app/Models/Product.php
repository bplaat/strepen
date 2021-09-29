<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $hidden = [
        'deleted'
    ];

    protected $casts = [
        'price' => 'double',
        'active' => 'boolean',
        'deleted' => 'boolean'
    ];

    // Generate a random image name
    public static function generateImageName($extension)
    {
        if ($extension == 'jpeg') $extension = 'jpg';
        $image = Str::random(32) . '.' . $extension;
        if (static::where('image', $image)->count() > 0) {
            return static::generateImageName($extension);
        }
        return $image;
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
        $inventories = $this->inventories()->where('deleted', false)->get();
        foreach ($inventories as $inventory) {
            $this->amount += $inventory->pivot->amount;
        }

        // Loop through all transactions and adjust amount
        $transactions = $this->transactions()->where('deleted', false)->get();
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

    // Turn model to api data
    public function forApi($user)
    {
        if ($this->image != null) {
            $this->image = asset('/storage/products/' . $this->image);
        }

        if ($user->role != User::ROLE_ADMIN) {
            unset($this->active);
            unset($this->created_at);
            unset($this->updated_at);
        }
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('deleted', false)
            ->where(function ($query) use ($searchQuery) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('description', 'LIKE', '%' . $searchQuery . '%');
            });
    }

    // Get amount chart data
    public function getAmountChart()
    {
        $amount = 0;
        $amountData = [];

        $inventories = $this->inventories()->where('deleted', false)->orderBy('created_at')->get();
        foreach ($inventories as $inventory) {
            $amount += $inventory->pivot->amount;
            $amountData[] = [ $inventory->created_at->format('Y-m-d'), $amount ];
        }

        $transactions = $this->transactions()->where('deleted', false)->orderBy('created_at')->get();
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
