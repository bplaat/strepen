<?php

namespace App\Http\Livewire\Admin\Transactions;

use App\Http\Livewire\PaginationComponent;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\NewDeposit;

class Crud extends PaginationComponent
{
    public $transaction;
    public $selectedProducts;
    public $isCreatingTransaction = false;
    public $isCreatingDeposit = false;
    public $isCreatingFood = false;

    public $rules = [
        'transaction.user_id' => 'required|integer|exists:users,id',
        'transaction.name' => 'required|min:2|max:48',
        'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
        'selectedProducts.*.amount' => 'required|integer|min:1',
        'transaction.price' => 'required|numeric'
    ];

    public $listeners = ['refresh' => '$refresh', 'userChooser', 'selectedProducts'];

    public function mount()
    {
        $this->transaction = new Transaction();
        $this->selectedProducts = collect();
    }

    public function userChooser($userId) {
        $this->transaction->user_id = $userId;
    }

    // Create transaction model
    public function openCreateTransaction()
    {
        $this->transaction->name = __('admin/transactions.crud.name_default_transaction') . ' ' . date('Y-m-d H:i:s');
        $this->isCreatingTransaction = true;
    }

    public function selectedProducts($selectedProducts)
    {
        if (!$this->isCreatingTransaction) return;
        $this->selectedProducts = collect($selectedProducts);

        // Validate input
        $this->validateOnly('transaction.user_id');
        $this->validateOnly('transaction.name');
        $this->validateOnly('selectedProducts.*.product_id');
        $this->validateOnly('selectedProducts.*.amount');
        if ($this->selectedProducts->count() == 0) return;

        // Create transaction
        $this->transaction->price = 0;
        foreach ($this->selectedProducts as $selectedProduct) {
            $this->transaction->price += $selectedProduct['product']['price'] * $selectedProduct['amount'];
        }
        $this->transaction->type = Transaction::TYPE_TRANSACTION;
        $this->transaction->save();

        // Attach products to transaction and decrement product amount
        foreach ($this->selectedProducts as $selectedProduct) {
            $product = Product::find($selectedProduct['product_id']);
            $this->transaction->products()->attach($product, [
                'amount' => $selectedProduct['amount']
            ]);
            $product->amount -= $selectedProduct['amount'];
            $product->save();
        }

        // Recalculate balance of user
        if ($this->transaction->user_id != 1) {
            $user = User::find($this->transaction->user_id);
            $user->balance -= $this->transaction->price;
            $user->save();
        }

        // Refresh page
        return redirect()->route('admin.transactions.crud');
    }

    public function createTransaction()
    {
        $this->emit('getSelectedProducts');
    }

    // Create deposit model
    public function openCreateDeposit()
    {
        $this->transaction->name = __('admin/transactions.crud.name_default_deposit') . ' ' . date('Y-m-d H:i:s');
        $this->isCreatingDeposit = true;
    }

    public function createDeposit()
    {
        // Validate input
        $this->validateOnly('transaction.user_id');
        $this->validateOnly('transaction.name');
        $this->validateOnly('transaction.price');
        if ($this->transaction->user_id == 1) return;

        // Create transaction
        $this->transaction->type = Transaction::TYPE_DEPOSIT;
        $this->transaction->save();

        // Recalculate balance of user
        $user = User::find($this->transaction->user_id);
        $user->balance += $this->transaction->price;
        $user->save();

        // Send user new deposit notification
        $user->notify(new NewDeposit($this->transaction));

        // Refresh page
        return redirect()->route('admin.transactions.crud');
    }

    // Create food model
    public function openCreateFood()
    {
        $this->transaction->name = __('admin/transactions.crud.name_default_food') . ' ' . date('Y-m-d H:i:s');
        $this->isCreatingFood = true;
    }

    public function createFood()
    {
        // Validate input
        $this->validateOnly('transaction.user_id');
        $this->validateOnly('transaction.name');
        $this->validateOnly('transaction.price');
        if ($this->transaction->user_id == 1) return;

        // Create transaction
        $this->transaction->type = Transaction::TYPE_FOOD;
        $this->transaction->save();

        // Recalculate balance of user
        $user = User::find($this->transaction->user_id);
        $user->balance -= $this->transaction->price;
        $user->save();

        // Refresh page
        return redirect()->route('admin.transactions.crud');
    }

    public function render()
    {
        return view('livewire.admin.transactions.crud', [
            'transactions' => Transaction::search(Transaction::select(), $this->query)
                ->with('products')
                ->orderBy('created_at', 'DESC')
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/transactions.crud.title')]);
    }
}
