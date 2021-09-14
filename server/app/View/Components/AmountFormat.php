<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AmountFormat extends Component
{
    public $amount;

    public function render()
    {
        return view('components.amount-format');
    }
}
