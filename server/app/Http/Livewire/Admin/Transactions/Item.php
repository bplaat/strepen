<?php

namespace App\Http\Livewire\Admin\Transactions;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Models\User;
use Livewire\Component;

class Item extends Component
{
    public $transaction;
    public $oldUserId;
    public $createdAtDate;
    public $createdAtTime;
    public $selectedProducts = [];
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'transaction.user_id' => 'required|integer|exists:users,id',
        'transaction.name' => 'required|min:2|max:48',
        'createdAtDate' => 'required|date_format:Y-m-d',
        'createdAtTime' => 'required|date_format:H:i:s',
        'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
        'selectedProducts.*.amount' => 'required|integer|min:1',
        'transaction.price' => 'required|numeric'
    ];

    public $listeners = ['inputValue'];

    public function mount()
    {
        $this->oldUserId = $this->transaction->user_id;

        $this->createdAtDate = $this->transaction->created_at->format('Y-m-d');
        $this->createdAtTime = $this->transaction->created_at->format('H:i:s');

        foreach ($this->transaction->products as $product) {
            $selectedProduct = [];
            $selectedProduct['product_id'] = $product->id;
            $selectedProduct['amount'] = $product->pivot->amount;
            $this->selectedProducts[] = $selectedProduct;
        }
    }

    public function inputValue($name, $value)
    {
        if ($name == 'item_user') {
            $this->transaction->user_id = $value;
        }

        if ($name == 'item_products') {
            $this->selectedProducts = $value;
        }
    }

    public function editTransaction()
    {
        // Validate same input
        $this->emit('inputValidate', 'item_user');
        if ($this->transaction->type == Transaction::TYPE_TRANSACTION) {
            $this->emit('inputValidate', 'item_products');
        }
        $this->validateOnly('transaction.user_id');
        $this->validateOnly('transaction.name');
        $this->validateOnly('createdAtDate');
        $this->validateOnly('createdAtTime');

        if ($this->transaction->type == Transaction::TYPE_TRANSACTION) {
            // Validate input
            $this->validateOnly('selectedProducts.*.product_id');
            $this->validateOnly('selectedProducts.*.amount');

            $selectedProducts = collect($this->selectedProducts)->map(function ($selectedProduct) {
                $product = Product::find($selectedProduct['product_id']);
                $product->selectedAmount = $selectedProduct['amount'];
                return $product;
            });

            if ($selectedProducts->count() == 0) {
                return;
            }

            // Edit transaction
            $this->transaction->price = 0;
            foreach ($selectedProducts as $product) {
                $this->transaction->price += $product->price * $product->selectedAmount;
            }

            // Detach and attach products to transaction
            $this->transaction->products()->detach();
            foreach ($selectedProducts as $product) {
                $this->transaction->products()->attach($product, [
                    'amount' => $product->selectedAmount
                ]);
            }

            // Recalculate amounts of all products (Very slow)
            foreach (Product::all() as $product) {
                $product->recalculateAmount();
                $product->save();
            }
        }

        if ($this->transaction->type == Transaction::TYPE_DEPOSIT || $this->transaction->type == Transaction::TYPE_FOOD) {
            // Validate input
            $this->validateOnly('transaction.price');
        }

        $this->transaction->created_at = $this->createdAtDate . ' ' . $this->createdAtTime;
        $this->transaction->save();

        // Recalculate old user balance
        if ($this->oldUserId != $this->transaction->user_id) {
            $user = User::find($this->oldUserId);
            $user->recalculateBalance();
            $user->save();
            $this->oldUserId = $this->transaction->user_id;
        }

        // Recalculate user balance
        $user = User::find($this->transaction->user_id);
        $user->recalculateBalance();
        $user->save();

        $this->isEditing = false;
        $this->emitUp('refresh');
    }

    public function deleteTransaction()
    {
        $this->isDeleting = false;

        // Delete and recalculate user balance
        $this->transaction->delete();

        $user = $this->transaction->user;
        $user->recalculateBalance();
        $user->save();

        // Recalculate amounts of all inventory products
        foreach ($this->transaction->products as $product) {
            $product->recalculateAmount();
            $product->save();
        }

        $this->emitUp('refresh');
    }

    public function render()
    {
        unset($this->transaction->user);
        unset($this->transaction->products);
        return view('livewire.admin.transactions.item');
    }
}
