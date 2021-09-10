<?php

namespace App\Http\Livewire\Admin\Transactions;

use App\Http\Livewire\PaginationComponent;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;

class Crud extends PaginationComponent
{
    public $users;
    public $products;
    public $transaction;
    public $transactionProducts;
    public $addProductId;
    public $isCreatingTransaction;
    public $isCreatingDeposit;

    public $rules = [
        'transaction.user_id' => 'required|integer|exists:users,id',
        'transaction.name' => 'required|min:2|max:48',
        'addProductId' => 'required|integer|exists:products,id',
        'transactionProducts.*.amount' => 'required|integer|min:1',
        'transaction.price' => 'required|numeric'
    ];

    public function mount()
    {
        $this->users = User::all()->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE);
        $this->products = Product::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
        $this->transaction = new Transaction();
        $this->transactionProducts = collect();
        $this->addProductId = null;
        $this->isCreatingTransaction = false;
        $this->isCreatingDeposit = false;
    }

    // Create transaction dialog
    public function openCreateTransaction()
    {
        $this->transaction->name = __('admin/transactions.crud.name_default_transaction') . ' ' . date('Y-m-d H:i:s');
        $this->isCreatingTransaction = true;
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

        // Update amounts of products
        foreach ($this->transactionProducts as $transactionProduct) {
            $product = Product::find($transactionProduct['product_id']);
            $product->amount -= $transactionProduct['amount'];
            $product->save();
        }

        // Recalculate balance of user
        $user = User::find($this->transaction->user_id);
        $user->balance -= $this->transaction->price;
        $user->save();

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

    // Create deposit dialog
    public function openCreateDeposit()
    {
        $this->transaction->name = __('admin/transactions.crud.name_default_deposit') . ' ' . date('Y-m-d H:i:s');
        $this->isCreatingDeposit = true;
    }

    public function createDeposit()
    {
        $this->validateOnly('transaction.user_id');
        $this->validateOnly('transaction.name');
        $this->validateOnly('transaction.price');

        $this->transaction->type = Transaction::TYPE_DEPOSIT;
        $this->transaction->save();

        $user = User::find($this->transaction->user_id);
        $user->balance += $this->transaction->price;
        $user->save();

        $this->mount();
    }

    public function render()
    {
        return view('livewire.admin.transactions.crud', [
            'transactions' => Transaction::search($this->q)
                ->with('products')
                ->orderBy('created_at', 'DESC')
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/transactions.crud.title')]);
    }
}
