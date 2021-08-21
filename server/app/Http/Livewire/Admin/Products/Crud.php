<?php

namespace App\Http\Livewire\Admin\Products;

use App\Http\Livewire\PaginationComponent;
use App\Models\Product;

class Crud extends PaginationComponent
{
    public $product;
    public $isCreating = false;

    public $rules = [
        'product.name' => 'required|min:2|max:48',
        'product.price' => 'required|numeric',
        'product.description' => 'nullable'
    ];

    public function mount()
    {
        $this->product = new Product();
    }

    public function createProduct()
    {
        $this->validate();
        $this->product->amount = 0;
        $this->product->save();
        $this->reset();
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
