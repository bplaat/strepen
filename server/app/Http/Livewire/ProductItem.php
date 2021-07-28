<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ProductItem extends Component
{
    public $product;
    public $isEditing = false;

    public $rules = [
        'product.name' => 'required|min:2|max:48',
        'product.price' => 'required|numeric',
        'product.description' => 'nullable'
    ];

    public function editProduct() {
        $this->isEditing = true;
    }

    public function updateProduct() {
        $this->validate();
        $this->isEditing = false;
        $this->product->save();
    }

    public function deleteProduct() {
        $this->product->delete();
        $this->emitUp('updateProducts');
    }

    public function render()
    {
        return view('livewire.product-item');
    }
}
