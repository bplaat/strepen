<?php

namespace App\Http\Livewire\Admin\Inventories;

use App\Models\User;
use App\Models\Product;
use App\Models\InventoryProduct;
use Livewire\Component;

class Item extends Component
{
    public $inventory;
    public $createdAtDate;
    public $createdAtTime;
    public $selectedProducts;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'inventory.user_id' => 'required|integer|exists:users,id',
        'inventory.name' => 'required|min:2|max:48',
        'createdAtDate' => 'required|date_format:Y-m-d',
        'createdAtTime' => 'required|date_format:H:i:s',
        'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
        'selectedProducts.*.amount' => 'required|integer|min:1'
    ];

    public $listeners = ['userChooser', 'selectedProducts'];

    public function mount()
    {
        $selectedProducts = InventoryProduct::where('inventory_id', $this->inventory->id)->get();
        $this->selectedProducts = collect();
        foreach ($selectedProducts as $selectedProduct) {
            $_selectedProduct = [];
            $_selectedProduct['product_id'] = $selectedProduct->product_id;
            $_selectedProduct['product'] = Product::find($selectedProduct->product_id);
            $_selectedProduct['amount'] = $selectedProduct->amount;
            $this->selectedProducts->push($_selectedProduct);
        }

        $this->createdAtDate = $this->inventory->created_at->format('Y-m-d');
        $this->createdAtTime = $this->inventory->created_at->format('H:i:s');
    }

    public function userChooser($userId) {
        $this->inventory->user_id = $userId;
    }

    public function selectedProducts($selectedProducts)
    {
        if (!$this->isEditing) return;
        $this->selectedProducts = collect($selectedProducts);

        // Validate input
        $this->validate();
        if (count($this->selectedProducts) == 0) return;

        // Edit inventory
        $this->inventory->price = 0;
        foreach ($this->selectedProducts as $selectedProduct) {
            $this->inventory->price += $selectedProduct['product']['price'] * $selectedProduct['amount'];
        }
        $this->inventory->created_at = $this->createdAtDate . ' ' . $this->createdAtTime;
        $this->inventory->save();

        // Detach and attach products to inventory
        $this->inventory->products()->detach();
        foreach ($this->selectedProducts as $selectedProduct) {
            $this->inventory->products()->attach($selectedProduct['product_id'], [
                'amount' => $selectedProduct['amount']
            ]);
        }

        // Recalculate amounts of all products (SLOW!!!)
        foreach (Product::all() as $product) {
            $product->recalculateAmount();
            $product->save();
        }

        $this->isEditing = false;
    }

    public function editInventory()
    {
        $this->emit('getSelectedProducts');
    }

    public function deleteInventory()
    {
        $this->isDeleting = false;
        $this->inventory->deleted = true;
        $this->inventory->save();

        // Recalculate amounts of all products (SLOW!!!)
        foreach (Product::all() as $product) {
            $product->recalculateAmount();
            $product->save();
        }

        $this->emitUp('refresh');
    }

    public function render()
    {
        unset($this->inventory->user);
        unset($this->inventory->products);
        return view('livewire.admin.inventories.item');
    }
}
