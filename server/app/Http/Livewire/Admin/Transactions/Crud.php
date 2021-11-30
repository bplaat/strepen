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
    public $userAmounts;
    public $isCreatingTransaction = false;
    public $isCreatingDeposit = false;
    public $creatingDepositTab = 'single';
    public $isCreatingFood = false;
    public $creatingFoodTab = 'single';

    public $rules = [
        'transaction.user_id' => 'required|integer|exists:users,id',
        'transaction.name' => 'required|min:2|max:48',
        'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
        'selectedProducts.*.amount' => 'required|integer|min:1',
        'transaction.price' => 'required|numeric',
        'userAmounts.*' => 'nullable|numeric'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->queryString['type'] = ['except' => ''];
        $this->queryString[] = 'user_id';
        $this->queryString[] = 'product_id';
        $this->listeners[] = 'inputValue';
        $this->listeners[] = 'selectedProducts';
    }

    public function mount()
    {
        if (
            $this->sort_by != 'created_at' && $this->sort_by != 'name' && $this->sort_by != 'name_desc' &&
            $this->sort_by != 'price_desc' && $this->sort_by != 'price'
        ) {
            $this->sort_by = null;
        }

        if ($this->type != 'transaction' && $this->type != 'deposit' && $this->type != 'food') {
            $this->type = null;
        }

        if ($this->user_id != 1 && User::where('id', $this->user_id)->where('deleted', false)->count() == 0) {
            $this->user_id = null;
        }

        if (Product::where('id', $this->product_id)->where('deleted', false)->count() == 0) {
            $this->product_id = null;
        }

        $this->transaction = new Transaction();
        $this->selectedProducts = collect();

        $this->users = User::where('active', true)->where('deleted', false)
            ->orderByRaw('active DESC, LOWER(firstname)')
            ->get();
        $this->userAmounts = array_fill(0, $this->users->count(), '');
    }

    public function inputValue($name, $value) {
        if ($name == 'user_filter') {
            $this->userIdTemp = $value;
        }

        if ($name == 'product_filter') {
            $this->productIdTemp = $value;
        }

        if ($name == 'user') {
            $this->transaction->user_id = $value;
        }
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
        $this->emit('inputValidate', 'user');
        $this->emit('validateComponents');
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
        $this->emit('inputClear', 'user');
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
        // Create single deposit
        if ($this->creatingDepositTab == 'single') {
            $this->emit('inputValidate', 'user');
            $this->validateOnly('transaction.name');
            $this->validateOnly('transaction.user_id');
            $this->validateOnly('transaction.price');

            // Create transaction
            $this->transaction->type = Transaction::TYPE_DEPOSIT;
            $this->transaction->save();

            // Recalculate balance of user
            $user = User::find($this->transaction->user_id);
            $user->balance += $this->transaction->price;
            $user->save();

            // Send user new deposit notification
            $user->notify(new NewDeposit($this->transaction));
        }

        // Create multiple deposits
        if ($this->creatingDepositTab == 'multiple') {
            $this->validateOnly('transaction.name');
            $this->validateOnly('userAmounts.*');

            // Create transaction
            foreach ($this->users as $index => $user) {
                $userAmount = $this->userAmounts[$index];
                if ($userAmount != '') {
                    // Create food transaciton for user
                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->type = Transaction::TYPE_DEPOSIT;
                    $transaction->name = $this->transaction->name;
                    $transaction->price = $userAmount;
                    $transaction->save();

                    // Recalculate balance of user
                    $user->balance += $transaction->price;
                    $user->save();

                    // Send user new deposit notification
                    $user->notify(new NewDeposit($transaction));
                }
            }
        }

        $this->emit('inputClear', 'user');
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

        // Create single deposit
        if ($this->creatingFoodTab == 'single') {
            $this->emit('inputValidate', 'user');
            $this->validateOnly('transaction.name');
            $this->validateOnly('transaction.user_id');
            $this->validateOnly('transaction.price');

            // Create transaction
            $this->transaction->type = Transaction::TYPE_FOOD;
            $this->transaction->save();

            // Recalculate balance of user
            $user = User::find($this->transaction->user_id);
            $user->balance -= $this->transaction->price;
            $user->save();
        }

        // Create multiple deposits
        if ($this->creatingFoodTab == 'multiple') {
            $this->validateOnly('transaction.name');
            $this->validateOnly('userAmounts.*');

            // Create transaction
            foreach ($this->users as $index => $user) {
                $userAmount = $this->userAmounts[$index];
                if ($userAmount != '') {
                    // Create food transaciton for user
                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->type = Transaction::TYPE_FOOD;
                    $transaction->name = $this->transaction->name;
                    $transaction->price = $userAmount;
                    $transaction->save();

                    // Recalculate balance of user
                    $user->balance -= $transaction->price;
                    $user->save();
                }
            }
        }

        $this->emit('inputClear', 'user');
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

        if ($this->sort_by == null) {
            $transactions = $transactions->orderBy('created_at', 'DESC');
        }
        if ($this->sort_by == 'created_at') {
            $transactions = $transactions->orderBy('created_at');
        }
        if ($this->sort_by == 'name') {
            $transactions = $transactions->orderByRaw('LOWER(name)');
        }
        if ($this->sort_by == 'name_desc') {
            $transactions = $transactions->orderByRaw('LOWER(name) DESC');
        }
        if ($this->sort_by == 'price_desc') {
            $transactions = $transactions->orderBy('price', 'DESC');
        }
        if ($this->sort_by == 'price') {
            $transactions = $transactions->orderBy('price');
        }

        return view('livewire.admin.transactions.crud', [
            'transactions' => $transactions->with('products')
                ->paginate(Setting::get('pagination_rows') * 3)->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/transactions.crud.title')]);
    }
}
