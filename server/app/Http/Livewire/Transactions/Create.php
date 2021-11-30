<?php

namespace App\Http\Livewire\Transactions;

use Livewire\Component;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $transaction;
    public $selectedProducts;
    public $isCreated;

    public function rules()
    {
        return [
            'transaction.name' => 'required|min:2|max:48',
            'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
            'selectedProducts.*.amount' => 'required|integer|min:1|max:' . Setting::get('max_stripe_amount')
        ];
    }

    public $listeners = ['inputValue'];

    public function mount()
    {
        $this->transaction = new Transaction();
        $this->transaction->name = __('transactions.create.name_default') . ' ' . date('Y-m-d H:i:s');
        $this->isCreated = false;
    }

    public function inputValue($name, $value)
    {
        if ($name == 'products') {
            $this->selectedProducts = $value;
        }
    }

    public function createTransaction()
    {
        // Validate input
        $this->emit('inputValidate', 'products');
        $this->validate();

        $selectedProducts = collect($this->selectedProducts)->map(function ($selectedProduct) {
            $product = Product::find($selectedProduct['product_id']);
            $product->selectedAmount = $selectedProduct['amount'];
            return $product;
        });

        if ($selectedProducts->count() == 0) return;

        if (Auth::user()->minor) {
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
        $this->transaction->user_id = Auth::id();
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

        // Recalculate balance of authed user
        $user = Auth::user();
        $user->balance -= $this->transaction->price;
        $user->save();

        $this->isCreated = true;
    }

    public function closeCreated()
    {
        $this->emit('inputClear', 'products');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.transactions.create')
            ->layout('layouts.app', ['title' => __('transactions.create.title')]);
    }
}
