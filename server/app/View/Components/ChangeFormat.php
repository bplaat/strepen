<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ChangeFormat extends Component
{
    public $change;
    public $isMoney;

    public function __construct($change, $isMoney = false)
    {
        $this->change = $change;
        $this->isMoney = $isMoney;
    }

    public function render()
    {
        return view('components.change-format');
    }
}
