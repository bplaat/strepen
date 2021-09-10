<?php

namespace App\Http\Livewire\Transactions;

use App\Http\Livewire\PaginationComponent;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class History extends PaginationComponent
{
    public function render()
    {
        return view('livewire.transactions.history', [
            'transactions' => Transaction::searchCollection(Auth::user()->transactions, $this->q)
                ->sortByDesc('created_at')
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('transactions.history.title')]);
    }
}
