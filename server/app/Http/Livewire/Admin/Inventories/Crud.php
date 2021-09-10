<?php

namespace App\Http\Livewire\Admin\Inventories;

use App\Http\Livewire\PaginationComponent;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class Crud extends PaginationComponent
{
    public $products;
    public $inventory;
    public $inventoryProducts;
    public $addProductId;
    public $isCreating;

    public $rules = [
        'inventory.name' => 'required|min:2|max:48',
        'addProductId' => 'required|integer|exists:products,id',
        'inventoryProducts.*.amount' => 'required|integer|min:1'
    ];

    public function mount()
    {
        $this->products = Product::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
        $this->inventory = new Inventory();
        $this->inventory->name = __('admin/inventories.crud.name_default') . ' ' . date('Y-m-d H:i:s');
        $this->inventoryProducts = collect();
        $this->addProductId = null;
        $this->isCreating = false;
    }

    public function createInventory()
    {
        $this->validateOnly('inventory.name');
        $this->validateOnly('inventoryProducts.*.amount');

        $this->inventory->user_id = Auth::id();
        $this->inventory->price = 0;
        foreach ($this->inventoryProducts as $inventoryProduct) {
            $this->inventory->price += $inventoryProduct['product']['price'] * $inventoryProduct['amount'];
        }
        $this->inventory->save();

        // Create product inventory pivot table items
        foreach ($this->inventoryProducts as $inventoryProduct) {
            if ($inventoryProduct['amount'] > 0) {
                $this->inventory->products()->attach($inventoryProduct['product_id'], [
                    'amount' => $inventoryProduct['amount']
                ]);
            }
        }

        // Recalculate amounts of all products
        foreach ($this->products as $product) {
            $product->recalculateAmount();
            $product->save();
        }

        $this->mount();
    }

    public function addProduct()
    {
        if ($this->addProductId != null) {
            $this->validateOnly('addProductId');

            $inventoryProduct = [];
            $inventoryProduct['product_id'] = $this->addProductId;
            $inventoryProduct['product'] = Product::find($this->addProductId);
            $inventoryProduct['amount'] = 0;
            $this->inventoryProducts->push($inventoryProduct);
            $this->addProductId = null;
        }
    }

    public function deleteProduct($productId)
    {
        $this->inventoryProducts = $this->inventoryProducts->where('product_id', '!=', $productId);
    }

    public function render()
    {
        return view('livewire.admin.inventories.crud', [
            'inventories' => Inventory::search($this->q)
                ->with(['user', 'products'])
                ->orderBy('created_at', 'DESC')
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/inventories.crud.title')]);
    }
}
