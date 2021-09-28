<?php

namespace App\Http\Livewire\Transactions;

use Livewire\Component;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $transaction;
    public $selectedProducts;
    public $isCreated;

    public $rules = [
        'transaction.name' => 'required|min:2|max:48',
        'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
        'selectedProducts.*.amount' => 'required|integer|min:1|max:24'
    ];

    public $listeners = ['selectedProducts'];

    public function mount()
    {
        $this->transaction = new Transaction();
        $this->transaction->name = __('transactions.create.name_default') . ' ' . date('Y-m-d H:i:s');
        $this->selectedProducts = collect();
        $this->isCreated = false;
    }

    public function selectedProducts($selectedProducts)
    {
        $this->selectedProducts = collect($selectedProducts);

        // Validate input
        $this->validate();
        if (count($this->selectedProducts) == 0) return;

        // Create transaction
        $this->transaction->price = 0;
        foreach ($this->selectedProducts as $selectedProduct) {
            $this->transaction->price += $selectedProduct['product']['price'] * $selectedProduct['amount'];
        }
        $this->transaction->user_id = Auth::id();
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

        // Recalculate balance of authed user
        $user = Auth::user();
        $user->balance -= $this->transaction->price;
        $user->save();

        $this->isCreated = true;
    }

    public function closeCreated()
    {
        $this->emit('clearSelectedProducts');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.transactions.create')
            ->layout('layouts.app', ['title' => __('transactions.create.title')]);
    }
}
