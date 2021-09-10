<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;

class Kiosk extends Component
{
    public $users;
    public $products;
    public $transaction;
    public $transactionProducts;
    public $addProductId;

    public $rules = [
        'transaction.user_id' => 'required|integer|exists:users,id',
        'transaction.name' => 'required|min:2|max:48',
        'addProductId' => 'required|integer|exists:products,id',
        'transactionProducts.*.amount' => 'required|integer|min:1'
    ];

    public function mount()
    {
        $this->users = User::all()->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE);
        $this->products = Product::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
        $this->transaction = new Transaction();
        $this->transaction->name = __('kiosk.name_default') . ' ' . date('Y-m-d H:i:s');
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

        // Recalculate amounts of all products
        foreach ($this->products as $product) {
            $product->recalculateAmount();
            $product->save();
        }

        // Recalculate balance of user
        $user = User::find($this->transaction->user_id);
        $user->recalculateBalance();
        $user->save();

        session()->flash('create_transaction_message', __('kiosk.success_message'));
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
        return view('livewire.kiosk')
            ->layout('layouts.app', ['title' => __('kiosk.title')]);
    }
}
