<?php

namespace App\Http\Livewire\Components;

use App\Models\Product;
use Livewire\Component;

class ProductsChooser extends Component
{
    public $selectedProducts;
    public $noMax = false;

    public $products;
    public $filteredProducts;
    public $productName;
    public $isOpen = false;

    public $listeners = ['getSelectedProducts'];

    public function mount()
    {
        $this->products = Product::where('active', true)->where('deleted', false)
            ->orderByRaw('LOWER(name)')->get();
        $this->filteredProducts = $this->products->filter(function ($product) {
            return !$this->selectedProducts->pluck('product_id')->contains($product->id);
        })->slice(0, 10);
    }

    public function getSelectedProducts()
    {
        $this->emitUp('selectedProducts', $this->selectedProducts);
    }

    public function updatedProductName() {
        if (!$this->isOpen) {
            $this->isOpen = true;
        }

        $this->filteredProducts = $this->products->filter(function ($product) {
            return !$this->selectedProducts->pluck('product_id')->contains($product->id) &&
                (strlen($this->productName) == 0 || stripos($product->name, $this->productName) !== false);
        })->slice(0, 10);
    }

    public function addFirstProduct() {
        if ($this->filteredProducts->count() > 0) {
            $this->addProduct($this->filteredProducts->first()->id);
        }
    }

    public function addProduct($productId)
    {
        $selectedProduct = [];
        $selectedProduct['product_id'] = $productId;
        $selectedProduct['product'] = $this->products->firstWhere('id', $productId);
        $selectedProduct['amount'] = 0;
        $this->selectedProducts->push($selectedProduct);
        $this->productName = null;
        $this->updatedProductName();
        $this->isOpen = false;
    }

    public function deleteProduct($productId)
    {
        $this->selectedProducts = $this->selectedProducts->where('product_id', '!=', $productId);
    }

    public function render()
    {
        return view('livewire.components.products-chooser');
    }
}
