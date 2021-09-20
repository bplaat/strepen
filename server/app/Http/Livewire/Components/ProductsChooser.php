<?php

namespace App\Http\Livewire\Components;

use App\Models\Product;
use Livewire\Component;

class ProductsChooser extends Component
{
    public $products;
    public $selectedProducts;
    public $nomax = false;
    public $addProductName;
    public $isOpen = false;

    public $rules = [
        'addProductName' => 'required|string|exists:products,name'
    ];

    public $listeners = ['getSelectedProducts'];

    public function mount()
    {
        $this->products = Product::where('active', true)->where('deleted', false)
            ->orderByRaw('LOWER(name)')->get();
    }

    public function getSelectedProducts()
    {
        $this->emitUp('selectedProducts', $this->selectedProducts);
    }

    public function addProduct($productId = null)
    {
        if ($productId != null) {
            $this->addProductName = $this->products->firstWhere('id', $productId)->name;
        }

        if ($this->addProductName != null) {
            $this->validate();

            $selectedProduct = [];
            $selectedProduct['product'] = $this->products->firstWhere('name', $this->addProductName);
            if ($selectedProduct['product'] == null) return;
            $selectedProduct['product_id'] = $selectedProduct['product']['id'];
            $selectedProduct['amount'] = 0;
            $this->selectedProducts->push($selectedProduct);
            $this->addProductName = null;
        }
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
