<?php

namespace App\Http\Livewire\Admin\Inventories;

use App\Models\User;
use App\Models\Product;
use App\Models\InventoryProduct;
use Livewire\Component;

class Item extends Component
{
    public $users;
    public $products;
    public $inventory;
    public $createdAtDate;
    public $createdAtTime;
    public $inventoryProducts;
    public $addProductId = null;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'inventory.user_id' => 'required|integer|exists:users,id',
        'inventory.name' => 'required|min:2|max:48',
        'createdAtDate' => 'required|date_format:Y-m-d',
        'createdAtTime' => 'required|date_format:H:i:s',
        'addProductId' => 'required|integer|exists:products,id',
        'inventoryProducts.*.amount' => 'required|integer|min:1'
    ];

    public function mount()
    {
        $this->users = User::all()->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE);
        $this->products = Product::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);

        $inventoryProducts = InventoryProduct::where('inventory_id', $this->inventory->id)->get();
        $this->inventoryProducts = collect();
        foreach ($inventoryProducts as $inventoryProduct) {
            $_inventoryProduct = [];
            $_inventoryProduct['product_id'] = $inventoryProduct->product_id;
            $_inventoryProduct['product'] = Product::find($inventoryProduct->product_id);
            $_inventoryProduct['amount'] = $inventoryProduct->amount;
            $this->inventoryProducts->push($_inventoryProduct);
        }

        $this->createdAtDate = $this->inventory->created_at->format('Y-m-d');
        $this->createdAtTime = $this->inventory->created_at->format('H:i:s');
    }

    public function editInventory()
    {
        $this->validateOnly('inventory.user_id');
        $this->validateOnly('inventory.name');
        $this->validateOnly('createdAtDate');
        $this->validateOnly('createdAtTime');
        $this->validateOnly('inventoryProducts.*.amount');

        $this->inventory->price = 0;
        foreach ($this->inventoryProducts as $inventoryProduct) {
            $this->inventory->price += $inventoryProduct['product']['price'] * $inventoryProduct['amount'];
        }

        $this->inventory->created_at = $this->createdAtDate . ' ' . $this->createdAtTime;

        // Reset all inventory products
        $this->inventory->products()->detach();
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

        $this->inventory->save();
        $this->isEditing = false;
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

    public function deleteInventory()
    {
        $this->isDeleting = false;
        $this->inventory->delete();

        // Recalculate amounts of all products
        foreach ($this->products as $product) {
            $product->recalculateAmount();
            $product->save();
        }

        $this->emitUp('refresh');
    }

    public function render()
    {
        unset($this->inventory->user);
        unset($this->inventory->products);
        return view('livewire.admin.inventories.item', [
            'sortedInventoryProducts' => $this->inventory->products->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
        ]);
    }
}
