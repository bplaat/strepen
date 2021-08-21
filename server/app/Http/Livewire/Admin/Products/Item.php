<?php

namespace App\Http\Livewire\Admin\Products;

use Livewire\Component;

class Item extends Component
{
    public $product;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'product.name' => 'required|min:2|max:48',
        'product.price' => 'required|numeric',
        'product.description' => 'nullable'
    ];

    public function editProduct()
    {
        $this->validate();
        $this->isEditing = false;
        $this->product->save();
    }

    public function deleteProduct()
    {
        $this->isDeleting = false;
        $this->product->delete();
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.products.item');
    }
}
