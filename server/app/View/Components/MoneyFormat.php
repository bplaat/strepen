<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MoneyFormat extends Component
{
    public $money;
    public $isBold;

    public function __construct($money, $isBold = true)
    {
        $this->money = $money;
        $this->isBold = $isBold;
    }

    public function render()
    {
        return view('components.money-format');
    }
}
