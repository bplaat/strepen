<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProductsAmounts extends Component
{
    public $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function render()
    {
        return view('components.products-amounts');
    }
}
