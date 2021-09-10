<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class Balance extends Component
{
    public function render()
    {
        return view('livewire.balance', [
            'balanceChart' => Auth::user()->getBalanceChart(),
        ])->layout('layouts.app', ['title' => __('balance.title'), 'chartjs' => true]);
    }
}
