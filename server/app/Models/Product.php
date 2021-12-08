<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $hidden = [
        'deleted'
    ];

    protected $casts = [
        'price' => 'double',
        'alcoholic' => 'boolean',
        'active' => 'boolean',
        'deleted' => 'boolean'
    ];

    protected $attributes = [
        'amount' => 0,
        'alcoholic' => false,
        'active' => true,
        'deleted' => false
    ];

    // Generate a random image name
    public static function generateImageName($extension)
    {
        if ($extension == 'jpeg') {
            $extension = 'jpg';
        }
        $image = Str::random(32) . '.' . $extension;
        if (static::where('image', $image)->count() > 0) {
            return static::generateImageName($extension);
        }
        return $image;
    }

    // Recalculate product amount
    public function recalculateAmount()
    {
        $this->amount = DB::table('inventory_product')
            ->join('inventories', 'inventories.id', 'inventory_id')
            ->where('deleted', false)
            ->where('product_id', $this->id)
            ->sum('amount')
            -
            DB::table('transaction_product')
            ->join('transactions', 'transactions.id', 'transaction_id')
            ->where('deleted', false)
            ->where('product_id', $this->id)
            ->sum('amount');
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
    public static function search($query, $searchQuery)
    {
        return $query->where('deleted', false)
            ->where(fn ($query) => $query->where('name', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('description', 'LIKE', '%' . $searchQuery . '%')
                ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%'));
    }

    // Convert product to API data
    public function toApiData($forUser = null, $includes = [])
    {
        $data = new \stdClass();
        $data->id = $this->id;
        $data->name = $this->name;
        $data->description = $this->description;
        $data->image = asset('/storage/products/' . ($this->image ?? Setting::get('default_product_image')));
        $data->price = $this->price;
        $data->alcoholic = $this->alcoholic;
        $data->created_at = $this->created_at;

        if (isset($this->pivot)) {
            $data->amount = $this->pivot->amount;
        }

        if ($forUser != null && ($forUser->role == User::ROLE_MANAGER || $forUser->role == User::ROLE_ADMIN)) {
            $data->inventory_amount = $this->amount;
            $data->active = $this->active;
            $data->updated_at = $this->updated_at;
        }

        if (in_array('inventories', $includes)) {
            $data->inventories = $this->inventories->map(fn ($inventory) => $inventory->toApiData($forUser));
        }

        if (in_array('transactions', $includes)) {
            $data->transactions = $this->transactions ->map(fn ($transaction) => $transaction->toApiData($forUser));
        }

        return $data;
    }

    // Get amount chart data
    public function getAmountChart($startDate, $endDate)
    {
        // Covert start and end date to timestamp
        $firstTransaction = $this->transactions()->where('deleted', false)->orderBy('created_at')->first();
        $firstInventory = $this->inventories()->where('deleted', false)->orderBy('created_at')->first();
        if ($firstTransaction != null || $firstInventory != null) {
            $oldestItem = $firstTransaction ?? $firstInventory;
            if ($firstTransaction != null && $firstInventory != null) {
                $firstTransaction->created_at->getTimestamp() < $firstInventory->created_at->getTimestamp()
                    ? $firstTransaction
                    : $firstInventory;
            }

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
        $days = ceil((($endDate + 24 * 60 * 60) - $startDate + 1) / (24 * 60 * 60));
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
