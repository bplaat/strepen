<?php

namespace App\Http\Livewire\Components;

use App\Models\Product;
use Livewire\Component;

class ProductsChooser extends Component
{
    public $products;
    public $selectedProducts;
    public $addProductId;

    public $rules = [
        'addProductId' => 'required|integer|exists:products,id'
    ];

    public $listeners = ['getSelectedProducts'];

    public function mount()
    {
        $this->products = Product::where('active', true)->get()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
    }

    public function getSelectedProducts()
    {
        $this->emitUp('selectedProducts', $this->selectedProducts);
    }

    public function addProduct()
    {
        if ($this->addProductId != null) {
            $this->validate();

            $selectedProduct = [];
            $selectedProduct['product_id'] = $this->addProductId;
            $selectedProduct['product'] = Product::find($this->addProductId);
            $selectedProduct['amount'] = 0;
            $this->selectedProducts->push($selectedProduct);
            $this->addProductId = null;
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
