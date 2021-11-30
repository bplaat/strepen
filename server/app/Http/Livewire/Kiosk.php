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
    public $isMinor = false;
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

    public $listeners = ['inputValue'];

    public function mount()
    {
        $this->transaction = new Transaction();
        $this->transaction->name = __('kiosk.name_default') . ' ' . date('Y-m-d H:i:s');
        $this->isCreated = false;
    }

    public function inputValue($name, $value) {
        if ($name == 'user') {
            $this->transaction->user_id = $value;

            $user = User::find($this->transaction->user_id);
            if ($user != null && $user->minor) {
                $this->isMinor = true;
                $this->emit('inputProps', 'products', [
                    'minor' => $this->isMinor
                ]);
            }
            if ($this->isMinor && ($user == null || !$user->minor)) {
                $this->isMinor = false;
                $this->emit('inputProps', 'products', [
                    'minor' => $this->isMinor
                ]);
            }
        }

        if ($name == 'products') {
            $this->selectedProducts = $value;
        }
    }

    public function createTransaction()
    {
        // Validate input
        $this->emit('inputValidate', 'user');
        $this->emit('inputValidate', 'products');
        $this->validate();

        $selectedProducts = collect($this->selectedProducts)->map(function ($selectedProduct) {
            $product = Product::find($selectedProduct['product_id']);
            $product->selectedAmount = $selectedProduct['amount'];
            return $product;
        });

        if ($selectedProducts->count() == 0) return;

        $user = User::find($this->transaction->user_id);
        if ($user->minor) {
            foreach ($selectedProducts as $product) {
                if ($product->alcoholic) {
                    return;
                }
            }
        }

        // Create transaction
        $this->transaction->price = 0;
        foreach ($selectedProducts as $product) {
            $this->transaction->price += $product->price * $product->selectedAmount;
        }
        $this->transaction->type = Transaction::TYPE_TRANSACTION;
        $this->transaction->save();

        // Attach products to transaction and decrement product amount
        foreach ($selectedProducts as $product) {
            $this->transaction->products()->attach($product, [
                'amount' => $product->selectedAmount
            ]);
            $product->amount -= $product->selectedAmount;
            unset($product->selectedAmount);
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
        $this->emit('inputClear', 'products');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.kiosk')
            ->layout('layouts.app', ['title' => __('kiosk.title')]);
    }
}
