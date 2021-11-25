<?php

namespace App\Http\Livewire\Admin\Products;

use App\Http\Livewire\PaginationComponent;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class Crud extends PaginationComponent
{
    use WithFileUploads;

    public $alcoholic;
    public $product;
    public $image;
    public $isCreating;

    public $rules = [
        'product.name' => 'required|min:2|max:48',
        'product.price' => 'required|numeric',
        'product.description' => 'nullable',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        'product.alcoholic' => 'nullable|boolean'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->queryString['alcoholic'] = ['except' => ''];
    }

    public function mount()
    {
        if (
            $this->sort_by != 'name_desc' && $this->sort_by != 'created_at' && $this->sort_by != 'created_at_desc' &&
            $this->sort_by != 'price_desc' && $this->sort_by != 'price'
        ) {
            $this->sort_by = null;
        }

        $this->product = new Product();
        $this->product->alcoholic = false;
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

        $this->product->save();
        $this->mount();
    }

    public function render()
    {
        $products = Product::search(Product::select(), $this->query);
        if ($this->alcoholic != null) {
            if ($this->alcoholic == 'yes') $alcoholic = true;
            if ($this->alcoholic == 'no') $alcoholic = false;
            $products = $products->where('alcoholic', $alcoholic);
        }

        if ($this->sort_by == null) {
            $products = $products->orderByRaw('active DESC, LOWER(name)');
        }
        if ($this->sort_by == 'name_desc') {
            $products = $products->orderByRaw('active DESC, LOWER(name) DESC');
        }
        if ($this->sort_by == 'created_at_desc') {
            $products = $products->orderBy('created_at', 'DESC');
        }
        if ($this->sort_by == 'created_at') {
            $products = $products->orderBy('created_at');
        }
        if ($this->sort_by == 'price_desc') {
            $products = $products->orderBy('price', 'DESC');
        }
        if ($this->sort_by == 'price') {
            $products = $products->orderBy('price');
        }

        return view('livewire.admin.products.crud', [
            'products' => $products->paginate(Setting::get('pagination_rows') * 4)->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/products.crud.title'), 'chartjs' => true]);
    }
}
