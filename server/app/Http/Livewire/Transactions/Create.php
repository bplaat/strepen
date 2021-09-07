<?php

namespace App\Http\Livewire\Transactions;

use Livewire\Component;

class Create extends Component
{
    public function render()
    {
        return view('livewire.transactions.create')
            ->layout('layouts.app', ['title' => __('transactions.create.title')]);
    }
}
