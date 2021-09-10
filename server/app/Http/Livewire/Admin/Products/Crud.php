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
    public $image;
    public $isCreating;

    public $rules = [
        'product.name' => 'required|min:2|max:48',
        'product.price' => 'required|numeric',
        'product.description' => 'nullable',
        'image' => 'nullable|image|max:1024'
    ];

    public function mount()
    {
        $this->product = new Product();
        $this->image = null;
        $this->isCreating = false;
    }

    public function createProduct()
    {
        $this->validate();

        if ($this->image != null) {
            $imageName = Product::generateImageName($this->image->extension());
            $this->image->storeAs('public/products', $imageName);
            $this->product->image = $imageName;
        }

        $this->product->amount = 0;
        $this->product->save();
        $this->mount();
    }

    public function render()
    {
        return view('livewire.admin.products.crud', [
            'products' => Product::search($this->q)
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/products.crud.title')]);
    }
}