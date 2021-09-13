<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SearchHeader extends Component
{
    public $itemName;

    public function render()
    {
        return view('components.search-header');
    }
}
