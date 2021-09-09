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
    public $products;
    public $transaction;
    public $oldUserId;
    public $createdAtDate;
    public $createdAtTime;
    public $transactionProducts;
    public $addProductId = null;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'transaction.user_id' => 'required|integer|exists:users,id',
        'transaction.name' => 'required|min:2|max:48',
        'createdAtDate' => 'required|date_format:Y-m-d',
        'createdAtTime' => 'required|date_format:H:i:s',
        'addProductId' => 'required|integer|exists:products,id',
        'transactionProducts.*.amount' => 'required|integer|min:1',
        'transaction.price' => 'required|numeric'
    ];

    public function mount()
    {
        $this->users = User::all()->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE);
        $this->products = Product::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
        $this->oldUserId = $this->transaction->user_id;

        $transactionProducts = TransactionProduct::where('transaction_id', $this->transaction->id)->get();
        $this->transactionProducts = collect();
        foreach ($transactionProducts as $transactionProduct) {
            $_transactionProduct = [];
            $_transactionProduct['product_id'] = $transactionProduct->product_id;
            $_transactionProduct['product'] = Product::find($transactionProduct->product_id);
            $_transactionProduct['amount'] = $transactionProduct->amount;
            $this->transactionProducts->push($_transactionProduct);
        }

        $this->createdAtDate = $this->transaction->created_at->format('Y-m-d');
        $this->createdAtTime = $this->transaction->created_at->format('H:i:s');
    }

    public function editTransaction()
    {
        $this->validateOnly('transaction.user_id');
        $this->validateOnly('transaction.name');
        $this->validateOnly('createdAtDate');
        $this->validateOnly('createdAtTime');

        if ($this->transaction->type == Transaction::TYPE_TRANSACTION) {
            $this->validateOnly('transactionProducts.*.amount');

            $this->transaction->price = 0;
            foreach ($this->transactionProducts as $transactionProduct) {
                $this->transaction->price += $transactionProduct['product']['price'] * $transactionProduct['amount'];
            }

            $this->transaction->created_at = $this->createdAtDate . ' ' . $this->createdAtTime;

            // Reset all transaction products
            $this->transaction->products()->detach();
            foreach ($this->transactionProducts as $transactionProduct) {
                if ($transactionProduct['amount'] > 0) {
                    $this->transaction->products()->attach($transactionProduct['product_id'], [
                        'amount' => $transactionProduct['amount']
                    ]);
                }
            }

            // Recalculate amounts of all products
            foreach ($this->products as $product) {
                $product->recalculateAmount();
                $product->save();
            }

        }

        if ($this->transaction->type == Transaction::TYPE_DEPOSIT) {
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

    public function deleteTransaction()
    {
        // Delete and recalculate user balance
        $userId = $this->transaction->user_id;
        $this->transaction->delete();
        $user = User::find($userId);
        $user->recalculateBalance();
        $user->save();

        $this->isDeleting = false;
        $this->emitUp('refresh');
    }

    public function render()
    {
        unset($this->transaction->user);
        unset($this->transaction->products);
        return view('livewire.admin.transactions.item', [
            'sortedTransactionProducts' => $this->transaction->products->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
        ]);
    }
}
