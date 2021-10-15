<?php

namespace App\Http\Livewire\Admin\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Item extends Component
{
    use WithFileUploads;

    public $product;
    public $image;
    public $isShowing = false;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'product.name' => 'required|min:2|max:48',
        'product.price' => 'required|numeric',
        'product.description' => 'nullable',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        'product.alcoholic' => 'nullable|boolean',
        'product.active' => 'nullable|boolean'
    ];

    public function editProduct()
    {
        $this->validate();

        if ($this->image != null) {
            $imageName = Product::generateImageName($this->image->extension());
            $this->image->storeAs('public/products', $imageName);

            if ($this->product->image != null) {
                Storage::delete('public/products/' . $this->product->image);
            }
            $this->product->image = $imageName;
        }

        $this->isEditing = false;
        $this->product->save();
    }

    public function deleteImage()
    {
        if ($this->product->image != null) {
            Storage::delete('public/products/' . $this->product->image);
        }
        $this->product->image = null;
        $this->product->save();
    }

    public function deleteProduct()
    {
        $this->isDeleting = false;
        $this->product->deleted = true;
        $this->product->save();
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.products.item');
    }
}
