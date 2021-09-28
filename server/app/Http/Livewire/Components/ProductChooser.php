<?php

namespace App\Http\Livewire\Components;

use App\Models\Product;
use Livewire\Component;

class ProductChooser extends Component
{
    public $productId;
    public $inline = false;
    public $relationship = false;

    public $products;
    public $filteredProducts;
    public $productName;
    public $product;
    public $isOpen = false;

    public $listeners = ['clearProductChooser'];

    public function mount()
    {
        $this->products = Product::where('active', true)->where('deleted', false)
            ->orderByRaw('LOWER(name)')->get();
        $this->filterProducts();

        if ($this->productId != null) {
            $this->selectProduct($this->productId);
        }
    }

    public function clearProductChooser()
    {
        $this->productName = '';
        $this->product = null;
        $this->emitUp('productChooser', null);
        $this->mount();
    }

    public function filterProducts()
    {
        $this->filteredProducts = $this->products->filter(function ($product) {
            return strlen($this->productName) == 0 || stripos($product->name, $this->productName) !== false;
        })->slice(0, 10);
    }

    public function updatedProductName()
    {
        $this->isOpen = true;
        if ($this->product != null && $this->productName != $this->product->name) {
            $this->product = null;
            $this->emitUp('productChooser', null);
        }
        $this->filterProducts();
    }

    public function selectFirstProduct()
    {
        if ($this->filteredProducts->count() > 0) {
            $this->product = $this->filteredProducts->first();
            $this->emitUp('productChooser', $this->product->id);
            $this->productName = $this->product->name;
            $this->filterProducts();
            $this->isOpen = false;
        }
    }

    public function selectProduct($productId) {
        $this->product = $this->products->firstWhere('id', $productId);
        $this->emitUp('productChooser', $this->product->id);
        $this->productName = $this->product->name;
        $this->filterProducts();
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.components.product-chooser');
    }
}
