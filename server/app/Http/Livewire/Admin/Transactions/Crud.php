<?php

namespace App\Http\Livewire\Admin\Transactions;

use App\Http\Livewire\PaginationComponent;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\NewDeposit;

class Crud extends PaginationComponent
{
    public $user_id;
    public $userIdTemp;
    public $type;
    public $product_id;
    public $productIdTemp;
    public $transaction;
    public $selectedProducts;
    public $users;
    public $foodAmounts;
    public $isCreatingTransaction = false;
    public $isCreatingDeposit = false;
    public $isCreatingFood = false;

    public $rules = [
        'transaction.user_id' => 'required|integer|exists:users,id',
        'transaction.name' => 'required|min:2|max:48',
        'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
        'selectedProducts.*.amount' => 'required|integer|min:1',
        'transaction.price' => 'required|numeric',
        'foodAmounts.*' => 'nullable|numeric'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->queryString['type'] = ['except' => ''];
        $this->queryString[] = 'user_id';
        $this->queryString[] = 'product_id';
        $this->listeners[] = 'userChooser';
        $this->listeners[] = 'productChooser';
        $this->listeners[] = 'selectedProducts';
    }

    public function mount()
    {
        if ($this->type != 'transaction' && $this->type != 'deposit' && $this->type != 'food') {
            $this->type = null;
        }

        if ($this->user_id != 1 && User::where('id', $this->user_id)->where('active', true)->where('deleted', false)->count() == 0) {
            $this->user_id = null;
        }

        if (Product::where('id', $this->product_id)->where('active', true)->where('deleted', false)->count() == 0) {
            $this->product_id = null;
        }

        $this->transaction = new Transaction();
        $this->selectedProducts = collect();

        $this->users = User::where('active', true)->where('deleted', false)
            ->orderByRaw('active DESC, LOWER(IF(lastname != \'\', IF(insertion != NULL, CONCAT(lastname, \', \', insertion, \' \', firstname), CONCAT(lastname, \' \', firstname)), firstname))')
            ->get();
        $this->foodAmounts = array_fill(0, $this->users->count(), '');
    }

    public function userChooser($userId) {
        $this->userIdTemp = $userId;
        $this->transaction->user_id = $userId;
    }

    public function productChooser($productId) {
        $this->productIdTemp = $productId;
    }

    public function search()
    {
        if ($this->type != 'transaction' && $this->type != 'deposit' && $this->type != 'food') {
            $this->type = null;
        }

        $this->user_id = $this->userIdTemp;
        $this->product_id = $this->productIdTemp;
        $this->resetPage();
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
        $this->emit('clearUserChooser');
        $this->emit('clearSelectedProducts');
        $this->mount();
        $this->isCreatingTransaction = false;
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

        $this->emit('clearUserChooser');
        $this->mount();
        $this->isCreatingDeposit = false;
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
        $this->validateOnly('transaction.name');
        $this->validateOnly('foodAmounts.*');

        // Create transaction
        foreach ($this->users as $index => $user) {
            $foodAmount = $this->foodAmounts[$index];
            if ($foodAmount != '') {
                // Create food transaciton for user
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->type = Transaction::TYPE_FOOD;
                $transaction->name = $this->transaction->name;
                $transaction->price = $foodAmount;
                $transaction->save();

                // Recalculate balance of user
                $user->balance -= $transaction->price;
                $user->save();
            }
        }

        $this->mount();
        $this->isCreatingFood = false;
    }

    public function render()
    {
        $transactions = Transaction::search(Transaction::select(), $this->query);
        if ($this->type != null) {
            if ($this->type == 'transaction') $type = Transaction::TYPE_TRANSACTION;
            if ($this->type == 'deposit') $type = Transaction::TYPE_DEPOSIT;
            if ($this->type == 'food') $type = Transaction::TYPE_FOOD;
            $transactions = $transactions->where('type', $type);
        }
        if ($this->user_id != null) {
            $transactions = $transactions->where('user_id', $this->user_id);
        }
        if ($this->product_id != null) {
            $transactions = $transactions->whereHas('products', function ($query) {
                return $query->where('product_id', $this->product_id);
            });
        }
        return view('livewire.admin.transactions.crud', [
            'transactions' => $transactions->with('products')
                ->orderBy('created_at', 'DESC')
                ->paginate(Setting::get('pagination_rows') * 4)->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/transactions.crud.title')]);
    }
}
