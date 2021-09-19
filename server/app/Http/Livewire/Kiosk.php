<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;

class Kiosk extends Component
{
    public $users;
    public $transaction;
    public $selectedProducts;

    public $rules = [
        'transaction.user_id' => 'required|integer|exists:users,id',
        'transaction.name' => 'required|min:2|max:48',
        'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
        'selectedProducts.*.amount' => 'required|integer|min:1'
    ];

    public $listeners = ['selectedProducts'];

    public function mount()
    {
        $this->users = User::where('active', true)->where('deleted', false)->get()
            ->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE);
        $this->transaction = new Transaction();
        $this->transaction->name = __('kiosk.name_default') . ' ' . date('Y-m-d H:i:s');
        $this->selectedProducts = collect();
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
        $user = User::find($this->transaction->user_id);
        $user->balance -= $this->transaction->price;
        $user->save();

        // Display create flash message and refresh
        session()->flash('create_transaction_message', __('kiosk.success_message'));
        $this->redirect('kiosk');
    }

    public function createTransaction()
    {
        $this->emit('getSelectedProducts');
    }

    public function render()
    {
        return view('livewire.kiosk')
            ->layout('layouts.app', ['title' => __('kiosk.title')]);
    }
}
