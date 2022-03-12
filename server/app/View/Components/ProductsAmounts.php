<?php

namespace App\View\Components;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class ProductsAmounts extends Component
{
    public $products;
    public $totalPrice;

    public function __construct($products, $totalPrice, $createdAt)
    {
        $this->products = $products;
        $this->totalPrice = $totalPrice;

        // If the total price is correct use current product prices
        $realTotalPrice = round($products->reduce(
            fn ($price, $product) => $price + $product->pivot->amount * $product->price
        ), 3);
        if ((string)$this->totalPrice == (string)$realTotalPrice) { // Do a string compare to fix some weird bug
            foreach ($this->products as $product) {
                $product->pivot->price = $product->price;
            }
            return;
        }

        // If the transaction only contains one product calculate then product price by division
        if ($this->products->count() == 1) {
            $this->products[0]->pivot->price = $this->totalPrice / $this->products[0]->pivot->amount;
            return;
        }

        // Else lookup old product price via older single product transaction
        $knownPrices = 0;
        $totalKnownPrice = 0;
        foreach ($this->products as $product) {
            $transactionProducts = DB::table('transaction_product')
                ->join('transactions', 'transactions.id', 'transaction_id')
                ->whereNull('deleted_at')
                ->where('user_id', '!=', 1)
                ->where('product_id', $product->id)
                ->where('transactions.created_at', '<', $createdAt)
                ->get();

            $product->pivot->price = 0;
            foreach ($transactionProducts as $transactionProduct) {
                $transaction = Transaction::where('id', $transactionProduct->transaction_id)->withCount('products')->first();
                if ($transaction->products_count == 1) {
                    $product->pivot->price = $transaction->price / $transactionProduct->amount;
                    $knownPrices++;
                    $totalKnownPrice += $product->pivot->price * $product->pivot->amount;
                    break;
                }
            }
        }

        // If own product price is missing calculate by division
        if ($knownPrices == $products->count() - 1) {
            foreach ($this->products as $product) {
                if ($product->pivot->price == 0) {
                    $product->pivot->price = ($totalPrice - $totalKnownPrice) / $product->pivot->amount;
                }
            }
        }
    }

    public function render()
    {
        return view('components.products-amounts');
    }
}
