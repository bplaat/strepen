<?php

namespace App\Http\Livewire\Components;

use App\Models\Product;

class ProductChooser extends InputComponent
{
    // Props
    public $productId;
    public $inline = false;
    public $relationship = false;
    public $includeInactive = false;

    // State
    public $products;
    public $filteredProducts;
    public $productName;
    public $product;
    public $isOpen = false;

    // Lifecycle
    public function mount()
    {
        $products = Product::where('deleted', false);
        if (!$this->includeInactive) {
            $products = $products->where('active', true);
        }
        $this->products = $products->orderByRaw('LOWER(name)')->get();
        $this->filterProducts();

        if ($this->productId != null) {
            $this->selectProduct($this->productId);
        }
    }

    public function filterProducts()
    {
        $this->filteredProducts = $this->products->filter(function ($product) {
            return strlen($this->productName) == 0 || stripos($product->name, $this->productName) !== false;
        })->slice(0, 10);
    }

    public function render()
    {
        return view('livewire.components.product-chooser');
    }

    // Events
    public function inputValidate($name)
    {
        if ($this->name == $name) {
            $this->valid = $this->product != null;
        }
    }

    public function inputClear($name)
    {
        if ($this->name == $name) {
            $this->productName = '';
            $this->product = null;
            $this->emitUp('inputValue', $this->name, null);
            $this->filterProducts();
            $this->isOpen = false;
        }
    }

    // Listeners
    public function updatedProductName()
    {
        $this->isOpen = true;
        if ($this->product != null && $this->productName != $this->product->name) {
            $this->product = null;
            $this->emitUp('inputValue', $this->name, null);
        }
        $this->filterProducts();
    }

    // Actions
    public function selectFirstProduct()
    {
        if ($this->filteredProducts->count() > 0) {
            $this->product = $this->filteredProducts->first();
            $this->productName = $this->product->name;
            $this->emitUp('inputValue', $this->name, $this->product->id);
            $this->filterProducts();
            $this->isOpen = false;
        }
    }

    public function selectProduct($productId) {
        $this->product = $this->products->firstWhere('id', $productId);
        $this->productName = $this->product->name;
        $this->emitUp('inputValue', $this->name, $this->product->id);
        $this->filterProducts();
        $this->isOpen = false;
    }
}
