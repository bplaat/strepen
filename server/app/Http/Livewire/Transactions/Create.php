<?php

namespace App\Http\Livewire\Transactions;

use Livewire\Component;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $products;
    public $transaction;
    public $transactionProducts;
    public $addProductId;

    public $rules = [
        'transaction.name' => 'required|min:2|max:48',
        'addProductId' => 'required|integer|exists:products,id',
        'transactionProducts.*.amount' => 'required|integer|min:1'
    ];

    public function mount()
    {
        $this->products = Product::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
        $this->transaction = new Transaction();
        $this->transaction->name = __('transactions.create.name_default') . ' ' . date('Y-m-d H:i:s');
        $this->transactionProducts = collect();
        $this->addProductId = null;
    }

    public function createTransaction()
    {
        $this->validateOnly('transaction.user_id');
        $this->validateOnly('transaction.name');
        $this->validateOnly('transactionProducts.*.amount');

        if ($this->transactionProducts->count() == 0) {
            return;
        }

        $this->transaction->price = 0;
        foreach ($this->transactionProducts as $transactionProduct) {
            $this->transaction->price += $transactionProduct['product']['price'] * $transactionProduct['amount'];
        }
        $this->transaction->user_id = Auth::id();
        $this->transaction->type = Transaction::TYPE_TRANSACTION;
        $this->transaction->save();

        // Create product transaction pivot table items
        foreach ($this->transactionProducts as $transactionProduct) {
            if ($transactionProduct['amount'] > 0) {
                $this->transaction->products()->attach($transactionProduct['product_id'], [
                    'amount' => $transactionProduct['amount']
                ]);
            }
        }

        // Update amounts of products
        foreach ($this->transactionProducts as $transactionProduct) {
            $product = Product::find($transactionProduct['product_id']);
            $product->amount -= $transactionProduct['amount'];
            $product->save();
        }

        // Recalculate balance of authed user
        $user = Auth::user();
        $user->balance -= $this->transaction->price;
        $user->save();

        session()->flash('create_transaction_message', __('transactions.create.success_message'));
        $this->mount();
    }

    public function addProduct()
    {
        if ($this->addProductId != null) {
            $this->validateOnly('addProductId');

            $transactionProduct = [];
            $transactionProduct['product_id'] = $this->addProductId;
            $transactionProduct['product'] = Product::find($this->addProductId);
            $transactionProduct['amount'] = 0;
            $this->transactionProducts->push($transactionProduct);
            $this->addProductId = null;
        }
    }

    public function deleteProduct($productId)
    {
        $this->transactionProducts = $this->transactionProducts->where('product_id', '!=', $productId);
    }

    public function render()
    {
        return view('livewire.transactions.create')
            ->layout('layouts.app', ['title' => __('transactions.create.title')]);
    }
}
