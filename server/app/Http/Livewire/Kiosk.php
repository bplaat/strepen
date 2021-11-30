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
    public $isMinor = false;

    public function rules()
    {
        return [
            'transaction.user_id' => 'required|integer|exists:users,id',
            'transaction.name' => 'required|min:2|max:48',
            'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
            'selectedProducts.*.amount' => 'required|integer|min:1|max:'. Setting::get('max_stripe_amount')
        ];
    }

    public $listeners = ['inputValue', 'selectedProducts'];

    public function mount()
    {
        $this->transaction = new Transaction();
        $this->transaction->name = __('kiosk.name_default') . ' ' . date('Y-m-d H:i:s');
        $this->selectedProducts = collect();
        $this->isCreated = false;
    }

    public function inputValue($name, $value) {
        if ($name == 'user') {
            $this->transaction->user_id = $value;

            $user = User::find($this->transaction->user_id);
            if ($user != null && $user->minor) {
                $this->isMinor = true;
                $this->emit('isMinorProducts');
            }
            if ($this->isMinor && ($user == null || !$user->minor)) {
                $this->isMinor = false;
                $this->emit('clearMinorProducts');
            }
        }
    }

    public function selectedProducts($selectedProducts)
    {
        $this->selectedProducts = collect($selectedProducts);

        // Validate input
        $this->emit('inputValidate', 'user');
        $this->emit('validateComponents');
        $this->validate();

        if (count($this->selectedProducts) == 0) return;

        $user = User::find($this->transaction->user_id);
        if ($user->minor) {
            foreach ($this->selectedProducts as $selectedProduct) {
                if ($selectedProduct['product']['alcoholic']) {
                    return;
                }
            }
        }

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
        $user->balance -= $this->transaction->price;
        $user->save();

        $this->isCreated = true;
    }

    public function closeCreated()
    {
        $this->emit('inputClear', 'user');
        $this->emit('clearSelectedProducts');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.kiosk')
            ->layout('layouts.app', ['title' => __('kiosk.title')]);
    }
}
