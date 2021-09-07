<?php

namespace App\Http\Livewire\Transactions;

use Livewire\Component;

class History extends Component
{
    public function render()
    {
        return view('livewire.transactions.history')
            ->layout('layouts.app', ['title' => __('transactions.history.title')]);
    }
}
