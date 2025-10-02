<?php

namespace App\Http\Livewire\Admin\Inventories;

use App\Http\Livewire\PaginationComponent;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Crud extends PaginationComponent
{
    public $user_id;
    public $userIdTemp;
    public $product_id;
    public $productIdTemp;
    public $inventory;
    public $selectedProducts = [];
    public $isCreating = false;

    public $rules = [
        'inventory.name' => 'required|min:2|max:48',
        'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
        'selectedProducts.*.amount' => 'required|integer|min:1'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->queryString[] = 'user_id';
        $this->queryString[] = 'product_id';
        $this->listeners[] = 'inputValue';
    }

    public function mount()
    {
        if (
            $this->sort_by != 'created_at' && $this->sort_by != 'name' && $this->sort_by != 'name_desc' &&
            $this->sort_by != 'price_desc' && $this->sort_by != 'price'
        ) {
            $this->sort_by = null;
        }

        if ($this->user_id != 1) {
            $user = User::where('id', $this->user_id)->withCount('inventories')->first();
            if ($user == null || $user->inventories_count == 0) {
                $this->user_id = null;
            }
        }

        if (Product::where('id', $this->product_id)->count() == 0) {
            $this->product_id = null;
        }

        $this->inventory = new Inventory();
        $this->inventory->name = __('admin/inventories.crud.name_default') . ' ' . date('Y-m-d H:i:s');
    }

    public function inputValue($name, $value)
    {
        if ($name == 'user_filter') {
            $this->userIdTemp = $value;
        }

        if ($name == 'product_filter') {
            $this->productIdTemp = $value;
        }

        if ($name == 'products') {
            $this->selectedProducts = $value;
        }
    }

    public function search()
    {
        $this->user_id = $this->userIdTemp;
        $this->product_id = $this->productIdTemp;
        $this->resetPage();
    }

    public function createInventory()
    {
        // Validate input
        $this->emit('inputValidate', 'products');
        $this->validate();

        $selectedProducts = collect($this->selectedProducts)->map(function ($selectedProduct) {
            $product = Product::find($selectedProduct['product_id']);
            $product->selectedAmount = $selectedProduct['amount'];
            return $product;
        });

        if ($selectedProducts->count() == 0) {
            return;
        }

        // Create inventory
        $this->inventory->user_id = Auth::id();
        $this->inventory->price = 0;
        foreach ($selectedProducts as $product) {
            $this->inventory->price += $product->price * $product->selectedAmount;
        }
        $this->inventory->save();

        // Create product inventory pivot table items
        foreach ($selectedProducts as $product) {
            $this->inventory->products()->attach($product, [
                'price' => $product->price,
                'amount' => $product->selectedAmount
            ]);
            $product->amount += $product->selectedAmount;
            unset($product->selectedAmount);
            $product->save();
        }

        // Refresh page
        $this->emit('inputClear', 'products');
        $this->mount();
        $this->isCreating = false;
    }

    public function render()
    {
        $inventories = Inventory::search(Inventory::select(), $this->query);
        if ($this->user_id != null) {
            $inventories = $inventories->where('user_id', $this->user_id);
        }
        if ($this->product_id != null) {
            $inventories = $inventories->whereHas('products', function ($query) {
                return $query->where('product_id', $this->product_id);
            });
        }

        if ($this->sort_by == null) {
            $inventories = $inventories->orderBy('created_at', 'DESC');
        }
        if ($this->sort_by == 'created_at') {
            $inventories = $inventories->orderBy('created_at');
        }
        if ($this->sort_by == 'name') {
            $inventories = $inventories->orderByRaw('LOWER(name)');
        }
        if ($this->sort_by == 'name_desc') {
            $inventories = $inventories->orderByRaw('LOWER(name) DESC');
        }
        if ($this->sort_by == 'price_desc') {
            $inventories = $inventories->orderBy('price', 'DESC');
        }
        if ($this->sort_by == 'price') {
            $inventories = $inventories->orderBy('price');
        }

        return view('livewire.admin.inventories.crud', [
            'inventories' => $inventories->with(['user', 'products'])
                ->paginate(Setting::get('pagination_rows') * 3)->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/inventories.crud.title')]);
    }
}
