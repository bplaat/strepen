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
        $products = Product::select();
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
        $this->filteredProducts = $this->products
            ->filter(fn ($product) => strlen($this->productName) == 0 || stripos($product->name, $this->productName) !== false)
            ->slice(0, 10);
    }

    public function emitValue()
    {
        $this->emitUp('inputValue', $this->name, $this->product != null ? $this->product->id : null);
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
            $this->emitValue();
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
            $this->emitValue();
        }
        $this->filterProducts();
    }

    // Actions
    public function selectFirstProduct()
    {
        if ($this->filteredProducts->count() > 0) {
            $this->selectProduct($this->filteredProducts->first()->id);
        }
    }

    public function selectProduct($productId)
    {
        $this->product = $this->products->firstWhere('id', $productId);
        if ($this->product == null) {
            $this->product = Product::withTrashed()->find($productId);
        }
        $this->productName = $this->product->name;
        $this->emitValue();
        $this->filterProducts();
        $this->isOpen = false;
    }
}
