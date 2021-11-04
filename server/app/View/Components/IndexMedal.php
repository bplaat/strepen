<?php

namespace App\View\Components;

use Illuminate\View\Component;

class IndexMedal extends Component
{
    public $index;

    public function __construct($index)
    {
        $this->index = $index;
    }

    public function render()
    {
        return view('components.index-medal');
    }
}
