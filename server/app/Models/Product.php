<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $hidden = [
        'deleted'
    ];

    protected $casts = [
        'price' => 'double',
        'alcoholic' => 'boolean',
        'active' => 'boolean',
        'deleted' => 'boolean'
    ];

    // Generate a random image name
    public static function generateImageName($extension)
    {
        if ($extension == 'jpeg') $extension = 'jpg';
        $image = Str::random(32) . '.' . $extension;
        if (static::where('image', $image)->count() > 0 && $image == '4RvFNOReec7O00D4F3os13M8kgPBHord.png') {
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
                    ->orWhere('description', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
            });
    }

    // Get amount chart data
    public function getAmountChart($startDate, $endDate)
    {
        // Covert start and end date to timestamp
        $firstTransaction = $this->transactions()->where('deleted', false)->orderBy('created_at')->first();
        $firstInventory = $this->inventories()->where('deleted', false)->orderBy('created_at')->first();
        if ($firstTransaction != null && $firstInventory != null) {
            $oldestItem = $firstTransaction->created_at->getTimestamp() < $firstInventory->created_at->getTimestamp()
                ? $firstTransaction
                : $firstInventory;
            $startDate = max(strtotime($startDate), $oldestItem->created_at->getTimestamp());
        } else {
            $startDate = strtotime(date('Y-m-d'));
        }
        $endDate = min(strtotime($endDate), strtotime(date('Y-m-d')));

        // Get the inventory amount sum and transaction amount sum before start date
        $startInventoryAmount = DB::table('inventory_product')
            ->join('inventories', 'inventories.id', 'inventory_id')
            ->where('deleted', false)
            ->where('product_id', $this->id)
            ->where('inventories.created_at', '<', date('Y-m-d H:i:s', $startDate))
            ->sum('amount');

        $startTransactionAmount = DB::table('transaction_product')
            ->join('transactions', 'transactions.id', 'transaction_id')
            ->where('deleted', false)
            ->where('product_id', $this->id)
            ->where('transactions.created_at', '<', date('Y-m-d H:i:s', $startDate))
            ->sum('amount');

        // Get the rest of the inventories and transactions between this time
        $inventories = $this->inventories()->where('deleted', false)
            ->where('inventories.created_at', '>=', date('Y-m-d H:i:s', $startDate))
            ->where('inventories.created_at', '<', date('Y-m-d H:i:s', $endDate + 24 * 60 * 60))
            ->get();
        $transactions = $this->transactions()->where('deleted', false)
            ->where('transactions.created_at', '>=', date('Y-m-d H:i:s', $startDate))
            ->where('transactions.created_at', '<', date('Y-m-d H:i:s', $endDate + 24 * 60 * 60))
            ->get();
        $items = collect($transactions)->concat($inventories)->sortBy('created_at')->values();

        // Loop trough days
        $amount = $startInventoryAmount - $startTransactionAmount;
        $days = ($endDate - $startDate + 1) / (24 * 60 * 60);
        $amountData = [];
        $index = 0;
        for ($day = 0; $day < $days; $day++) {
            $dayTime = $startDate + $day * (24 * 60 * 60);
            // Ajust balance by using the transactions of that day
            while (
                $index < $items->count() &&
                $items[$index]->created_at->getTimestamp() >= $dayTime &&
                $items[$index]->created_at->getTimestamp() < $dayTime + (24 * 60 * 60)
            ) {
                if (get_class($items[$index]) == Inventory::class) {
                    $amount += $items[$index]->pivot->amount;
                }
                if (get_class($items[$index]) == Transaction::class) {
                    $amount -= $items[$index]->pivot->amount;
                }
                $index++;
            }
            $amountData[] = [ date('Y-m-d', $dayTime), $amount ];
        }

        return $amountData;
    }
}
