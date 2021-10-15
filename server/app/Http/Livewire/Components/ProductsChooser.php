<?php

namespace App\Http\Livewire\Components;

use App\Models\Product;
use Livewire\Component;

class ProductsChooser extends Component
{
    public $selectedProducts;
    public $noMax = false;
    public $isMinor = false;

    public $products;
    public $filteredProducts;
    public $productName;
    public $isOpen = false;

    public $listeners = ['getSelectedProducts', 'clearSelectedProducts', 'isMinorProducts', 'clearMinorProducts'];

    public function mount()
    {
        $this->products = Product::where('active', true)->where('deleted', false)
            ->orderByRaw('LOWER(name)')->get();

        $this->filteredProducts = $this->products->filter(function ($product) {
            return !$this->selectedProducts->pluck('product_id')->contains($product->id);
        });
        if ($this->isMinor) {
            $this->filteredProducts = $this->filteredProducts->filter(function ($product) {
                return !$product->alcoholic;
            });
        }
        $this->filteredProducts = $this->filteredProducts->slice(0, 10);
    }

    public function getSelectedProducts()
    {
        $this->emitUp('selectedProducts', $this->selectedProducts);
    }

    public function clearSelectedProducts()
    {
        $this->selectedProducts = collect();
        $this->mount();
    }

    public function isMinorProducts() {
        $this->isMinor = true;
        $this->selectedProducts = $this->selectedProducts->where('product.alcoholic', '==', false);
        $this->filterProducts();
    }

    public function clearMinorProducts() {
        $this->isMinor = false;
        $this->filterProducts();
    }

    public function filterProducts()
    {
        $this->filteredProducts = $this->products->filter(function ($product) {
            return !$this->selectedProducts->pluck('product_id')->contains($product->id) &&
                (strlen($this->productName) == 0 || stripos($product->name, $this->productName) !== false);
        });
        if ($this->isMinor) {
            $this->filteredProducts = $this->filteredProducts->filter(function ($product) {
                return !$product->alcoholic;
            });
        }
        $this->filteredProducts = $this->filteredProducts->slice(0, 10);
    }

    public function updatedProductName()
    {
        if (!$this->isOpen) $this->isOpen = true;
        $this->filterProducts();
    }

    public function addFirstProduct()
    {
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
        $this->filterProducts();
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
