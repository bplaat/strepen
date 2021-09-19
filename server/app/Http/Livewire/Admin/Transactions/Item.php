<?php

namespace App\Http\Livewire\Admin\Transactions;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Models\User;
use Livewire\Component;

class Item extends Component
{
    public $users;
    public $transaction;
    public $oldUserId;
    public $createdAtDate;
    public $createdAtTime;
    public $selectedProducts;
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

    public $listeners = ['selectedProducts'];

    public function mount()
    {
        $this->users = User::where('deleted', false)->where(function ($query) {
                return $query->where('active', true)->orWhere('id', 1);
            })->get()
            ->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE);
        $this->oldUserId = $this->transaction->user_id;

        $selectedProducts = TransactionProduct::where('transaction_id', $this->transaction->id)->get();
        $this->selectedProducts = collect();
        foreach ($selectedProducts as $selectedProduct) {
            $_selectedProduct = [];
            $_selectedProduct['product_id'] = $selectedProduct->product_id;
            $_selectedProduct['product'] = Product::find($selectedProduct->product_id);
            $_selectedProduct['amount'] = $selectedProduct->amount;
            $this->selectedProducts->push($_selectedProduct);
        }

        $this->createdAtDate = $this->transaction->created_at->format('Y-m-d');
        $this->createdAtTime = $this->transaction->created_at->format('H:i:s');
    }

    public function selectedProducts($selectedProducts)
    {
        if (!$this->isEditing) return;
        $this->selectedProducts = collect($selectedProducts);

        // Validate same input
        $this->validateOnly('transaction.user_id');
        $this->validateOnly('transaction.name');
        $this->validateOnly('createdAtDate');
        $this->validateOnly('createdAtTime');

        if ($this->transaction->type == Transaction::TYPE_TRANSACTION) {
            // Validate input
            $this->validateOnly('selectedProducts.*.product_id');
            $this->validateOnly('selectedProducts.*.amount');
            if (count($this->selectedProducts) == 0) return;

            // Edit transaction
            $this->transaction->price = 0;
            foreach ($this->selectedProducts as $selectedProduct) {
                $this->transaction->price += $selectedProduct['product']['price'] * $selectedProduct['amount'];
            }

            // Detach and attach products to transaction
            $this->transaction->products()->detach();
            foreach ($this->selectedProducts as $selectedProduct) {
                $this->transaction->products()->attach($selectedProduct['product_id'], [
                    'amount' => $selectedProduct['amount']
                ]);
            }

            // Recalculate amounts of all products (SLOW!!!)
            foreach (Product::all() as $product) {
                $product->recalculateAmount();
                $product->save();
            }
        }

        if ($this->transaction->type == Transaction::TYPE_DEPOSIT) {
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
    }

    public function editTransaction()
    {
        if ($this->transaction->type == Transaction::TYPE_TRANSACTION) {
            $this->emit('getSelectedProducts');
        }

        if ($this->transaction->type == Transaction::TYPE_DEPOSIT) {
            $this->selectedProducts([]);
        }
    }

    public function deleteTransaction()
    {
        $this->isDeleting = false;

        // Delete and recalculate user balance
        $userId = $this->transaction->user_id;
        $this->transaction->deleted = true;
        $this->transaction->save();

        $user = User::find($userId);
        $user->recalculateBalance();
        $user->save();

        // Recalculate amounts of all products (SLOW!!!)
        foreach (Product::all() as $product) {
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
