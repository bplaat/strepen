<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MoneyFormat extends Component
{
    public $money;

    public function render()
    {
        return view('components.money-format');
    }
}
