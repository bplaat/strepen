<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;

class Kiosk extends Component
{
    public $transaction;
    public $selectedProducts;
    public $isCreated;

    public function rules()
    {
        return [
            'transaction.user_id' => 'required|integer|exists:users,id',
            'transaction.name' => 'required|min:2|max:48',
            'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
            'selectedProducts.*.amount' => 'required|integer|min:1|max:'. Setting::get('max_stripe_amount')
        ];
    }

    public $listeners = ['userChooser', 'selectedProducts'];

    public function mount()
    {
        $this->transaction = new Transaction();
        $this->transaction->name = __('kiosk.name_default') . ' ' . date('Y-m-d H:i:s');
        $this->selectedProducts = collect();
        $this->isCreated = false;
    }

    public function userChooser($userId) {
        $this->transaction->user_id = $userId;
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

        $this->isCreated = true;
    }

    public function closeCreated()
    {
        $this->emit('clearUserChooser');
        $this->emit('clearSelectedProducts');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.kiosk')
            ->layout('layouts.app', ['title' => __('kiosk.title')]);
    }
}
