<?php

namespace App\Http\Livewire\Admin\Products;

use App\Http\Livewire\PaginationComponent;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class Crud extends PaginationComponent
{
    use WithFileUploads;

    public $product;
    public $productImage;
    public $isCreating;

    public $rules = [
        'product.name' => 'required|min:2|max:48',
        'product.price' => 'required|numeric',
        'product.description' => 'nullable',
        'productImage' => 'nullable|image|max:1024'
    ];

    public function mount()
    {
        $this->product = new Product();
        $this->productImage = null;
        $this->isCreating = false;
    }

    public function createProduct()
    {
        $this->validate();

        if ($this->productImage != null) {
            $imageName = Product::generateImageName($this->productImage->extension());
            $this->productImage->storeAs('public/products', $imageName);
            $this->product->image = $imageName;
        }

        $this->product->amount = 0;
        $this->product->save();
        $this->mount();
    }

    public function render()
    {
        return view('livewire.admin.products.crud', [
            'products' => Product::search($this->q)->get()
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.livewire', ['title' => __('admin/products.crud.title')]);
    }
}
