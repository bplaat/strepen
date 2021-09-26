<?php

namespace App\Http\Livewire\Admin\Inventories;

use App\Http\Livewire\PaginationComponent;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class Crud extends PaginationComponent
{
    public $inventory;
    public $selectedProducts;
    public $isCreating = false;

    public $rules = [
        'inventory.name' => 'required|min:2|max:48',
        'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
        'selectedProducts.*.amount' => 'required|integer|min:1'
    ];

    public $listeners = ['refresh' => '$refresh', 'selectedProducts'];

    public function mount()
    {
        $this->inventory = new Inventory();
        $this->inventory->name = __('admin/inventories.crud.name_default') . ' ' . date('Y-m-d H:i:s');
        $this->selectedProducts = collect();
    }

    public function selectedProducts($selectedProducts)
    {
        if (!$this->isCreating) return;
        $this->selectedProducts = collect($selectedProducts);

        // Validate input
        $this->validate();
        if (count($this->selectedProducts) == 0) return;

        // Create inventory
        $this->inventory->user_id = Auth::id();
        $this->inventory->price = 0;
        foreach ($this->selectedProducts as $selectedProduct) {
            $this->inventory->price += $selectedProduct['product']['price'] * $selectedProduct['amount'];
        }
        $this->inventory->save();

        // Create product inventory pivot table items
        foreach ($this->selectedProducts as $selectedProduct) {
            if ($selectedProduct['amount'] > 0) {
                $product = Product::find($selectedProduct['product_id']);
                $this->inventory->products()->attach($product, [
                    'amount' => $selectedProduct['amount']
                ]);
                $product->amount += $selectedProduct['amount'];
                $product->save();
            }
        }

        // Refresh page
        $this->emit('clearSelectedProducts');
        $this->mount();
        $this->isCreating = false;
    }

    public function createInventory()
    {
        $this->emit('getSelectedProducts');
    }

    public function render()
    {
        return view('livewire.admin.inventories.crud', [
            'inventories' => Inventory::search(Inventory::select(), $this->query)
                ->with(['user', 'products'])
                ->orderBy('created_at', 'DESC')
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/inventories.crud.title')]);
    }
}
