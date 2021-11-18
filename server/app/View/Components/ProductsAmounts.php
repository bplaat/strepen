<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProductsAmounts extends Component
{
    public $products;
    public $totalPrice;
    public $realTotalPrice;

    public function __construct($products, $totalPrice = null)
    {
        $this->products = $products;
        $this->totalPrice = $totalPrice;
        $this->realTotalPrice = round($products->reduce(function ($price, $product) {
            return $price + $product->pivot->amount * $product->price;
        }), 3);
    }

    public function render()
    {
        return view('components.products-amounts');
    }
}
