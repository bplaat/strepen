<?php

namespace App\Http\Livewire\Transactions;

use App\Http\Livewire\PaginationComponent;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class History extends PaginationComponent
{
    public $type;
    public $product_id;
    public $productIdTemp;

    public function __construct()
    {
        parent::__construct();
        $this->queryString['type'] = ['except' => ''];
        $this->queryString[] = 'product_id';
        $this->listeners[] = 'productChooser';
    }

    public function mount()
    {
        if ($this->type != 'transaction' && $this->type != 'deposit' && $this->type != 'food') {
            $this->type = null;
        }

        if (Product::where('id', $this->product_id)->where('active', true)->where('deleted', false)->count() == 0) {
            $this->product_id = null;
        }
    }

    public function productChooser($productId) {
        $this->productIdTemp = $productId;
    }

    public function search()
    {
        if ($this->type != 'transaction' && $this->type != 'deposit' && $this->type != 'food') {
            $this->type = null;
        }

        $this->product_id = $this->productIdTemp;
        $this->resetPage();
    }

    public function render()
    {
        $transactions = Transaction::search(Auth::user()->transactions(), $this->query);
        if ($this->type != null) {
            if ($this->type == 'transaction') $type = Transaction::TYPE_TRANSACTION;
            if ($this->type == 'deposit') $type = Transaction::TYPE_DEPOSIT;
            if ($this->type == 'food') $type = Transaction::TYPE_FOOD;
            $transactions = $transactions->where('type', $type);
        }
        if ($this->product_id != null) {
            $transactions = $transactions->whereHas('products', function ($query) {
                return $query->where('product_id', $this->product_id);
            });
        }
        return view('livewire.transactions.history', [
            'transactions' => $transactions->orderBy('created_at', 'DESC')
                ->paginate(Setting::get('pagination_rows') * 3)->withQueryString()
        ])->layout('layouts.app', ['title' => __('transactions.history.title')]);
    }
}
