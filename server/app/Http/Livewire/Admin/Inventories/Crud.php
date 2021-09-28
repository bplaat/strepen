<?php

namespace App\Http\Livewire\Admin\Inventories;

use App\Http\Livewire\PaginationComponent;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Crud extends PaginationComponent
{
    public $user_id;
    public $userIdTemp;
    public $product_id;
    public $productIdTemp;
    public $inventory;
    public $selectedProducts;
    public $isCreating = false;

    public $rules = [
        'inventory.name' => 'required|min:2|max:48',
        'selectedProducts.*.product_id' => 'required|integer|exists:products,id',
        'selectedProducts.*.amount' => 'required|integer|min:1'
    ];

    public function __construct() {
        parent::__construct();
        $this->queryString[] = 'user_id';
        $this->queryString[] = 'product_id';
        $this->listeners[] = 'userChooser';
        $this->listeners[] = 'productChooser';
        $this->listeners[] = 'selectedProducts';
    }

    public function mount()
    {
        if ($this->user_id != 1 && User::where('id', $this->user_id)->where('active', true)->where('deleted', false)->count() == 0) {
            $this->user_id = null;
        }
        if (Product::where('id', $this->product_id)->where('active', true)->where('deleted', false)->count() == 0) {
            $this->product_id = null;
        }

        $this->inventory = new Inventory();
        $this->inventory->name = __('admin/inventories.crud.name_default') . ' ' . date('Y-m-d H:i:s');
        $this->selectedProducts = collect();
    }

    public function userChooser($userId) {
        $this->userIdTemp = $userId;
    }

    public function productChooser($productId) {
        $this->productIdTemp = $productId;
    }

    public function search()
    {
        $this->user_id = $this->userIdTemp;
        $this->product_id = $this->productIdTemp;
        $this->resetPage();
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
        return view('livewire.admin.inventories.crud', [
            'inventories' => $inventories->with(['user', 'products'])
                ->orderBy('created_at', 'DESC')
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/inventories.crud.title')]);
    }
}
