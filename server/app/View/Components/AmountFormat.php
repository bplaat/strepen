<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AmountFormat extends Component
{
    public $amount;
    public $isBold;

    public function __construct($amount, $isBold = true)
    {
        $this->amount = $amount;
        $this->isBold = $isBold;
    }

    public function render()
    {
        return view('components.amount-format');
    }
}
