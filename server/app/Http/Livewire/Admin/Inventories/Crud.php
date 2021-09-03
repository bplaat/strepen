<?php

namespace App\Http\Livewire\Admin\Inventories;

use App\Http\Livewire\PaginationComponent;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\InventoryProduct;
use Illuminate\Support\Facades\Auth;

class Crud extends PaginationComponent
{
    public $products;
    public $inventory;
    public $productId;
    public $inventoryProducts;
    public $isCreating;

    public $rules = [
        // 'inventory.user_id' => 'required|integer|unique:users,id',
        'inventory.name' => 'required|min:2|max:48',
        'productId' => 'required|integer|exists:products,id'
    ];

    public function mount()
    {
        $this->products = Product::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
        $this->inventory = new Inventory();
        $this->productId = null;
        $this->inventoryProducts = collect();
        $this->isCreating = false;
    }

    public function createInventory()
    {
        $this->validateOnly('inventory.name');

        $this->inventory->user_id = Auth::id();
        $this->inventory->price = 0;
        $this->inventory->save();

        foreach ($this->inventoryProducts as $inventoryProduct) {
            if ($inventoryProduct['amount'] > 0) {
                $this->inventory->products()->attach($inventoryProduct['product_id'], [
                    'amount' => $inventoryProduct['amount']
                ]);
            }
        }

        $this->mount();
    }

    public function addProduct()
    {
        if ($this->productId != null) {
            $this->validateOnly('productId');

            $inventoryProduct = new InventoryProduct();
            $inventoryProduct->product_id = $this->productId;
            $inventoryProduct->amount = 0;
            $this->inventoryProducts->push($inventoryProduct);
            $this->productId = null;
        }
    }

    public function deleteProduct($productId)
    {
        $this->inventoryProducts = $this->inventoryProducts->where('product_id', '!=', $productId);
    }

    public function render()
    {
        return view('livewire.admin.inventories.crud', [
            'inventories' => Inventory::search($this->q)->get()
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.livewire', ['title' => __('admin/inventories.crud.title')]);
    }
}
