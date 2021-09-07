<?php

namespace App\Http\Livewire\Admin\Transactions;

use App\Http\Livewire\PaginationComponent;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Crud extends PaginationComponent
{
    public $users;
    public $transaction;
    public $isCreatingTransaction;
    public $isCreatingDeposit;

    public $rules = [
        'transaction.user_id' => 'required|integer|exists:users,id',
        'transaction.name' => 'required|min:2|max:48',
        'transaction.price' => 'required|numeric'
    ];

    public function mount()
    {
        $this->users = User::all()->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE);
        $this->transaction = new Transaction();
        $this->transaction->name = __('admin/transactions.crud.name_default') . ' ' . date('Y-m-d H:i:s');
        $this->isCreatingTransaction = false;
        $this->isCreatingDeposit = false;
    }

    public function createTransaction()
    {
        // TODO
    }

    public function createDeposit()
    {
        $this->validate();
        $this->transaction->type = Transaction::TYPE_DEPOSIT;
        $this->transaction->save();

        $user = User::find($this->transaction->user_id);
        $user->money += $this->transaction->price;
        $user->save();

        $this->mount();
    }

    public function render()
    {
        return view('livewire.admin.transactions.crud', [
            'transactions' => Transaction::search($this->q)
                ->sortByDesc('created_at')
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/transactions.crud.title')]);
    }
}
