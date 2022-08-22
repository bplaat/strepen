<?php

namespace App\Http\Livewire;

use App\Models\Inventory;
use App\Models\Setting;
use App\Models\Transaction;
use Livewire\Component;

class Casino extends Component
{
    public function render()
    {
        return view('livewire.casino')->layout('layouts.app', ['title' => __('casino.title')]);
    }
}
