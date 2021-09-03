<?php

namespace App\Http\Livewire\Admin\Inventories;

use App\Http\Livewire\PaginationComponent;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\InventoryProduct;

class Crud extends PaginationComponent
{
    public $inventory;
    public $isCreating = false;

    public $products;
    public $productId;
    public $productAmount;
    public $inventoryProducts;

    // public $rules = [
    //     'productId' => 'integer|unique:product,id',
    //     'productAmount' => 'required|integer|min:1',
    //     'inventory.user_id' => 'required|integer|unique:users,id',
    //     'inventory.name' => 'required|min:2|max:48',
    // ];

    public function mount()
    {
        $this->inventory = new Inventory();
        $this->products = Product::all();
        $this->inventoryProducts = collect();
    }

    public function createInventory()
    {
        // $this->validate();
        // $this->inventory->save();
        // $this->reset();
    }

    public function addProduct()
    {
        $inventoryProduct = new InventoryProduct();
        $inventoryProduct->product_id = $this->productId;
        $inventoryProduct->amount = 0;
        $this->inventoryProducts->push($inventoryProduct);
        // if ($this->inventoryProducts->count() > 1) {
        //     dd($this->inventoryProducts);
        // }
        $this->productId = null;
    }

    public function updateProduct($productId)
    {
        foreach ($this->inventoryProducts as $inventoryProduct) {
            if ($inventoryProduct->product_id == $productId) {
                $inventoryProduct->amount = $this->productAmount;
                break;
            }
        }
        $this->productAmount = null;
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
