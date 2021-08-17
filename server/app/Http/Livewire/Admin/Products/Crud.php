<?php

namespace App\Http\Livewire\Admin\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Crud extends Component
{
    use WithPagination;

    public function paginationView()
    {
        return 'pagination';
    }

    public $q;
    public $queryString = ['q'];

    public $isCreating = false;
    public $productName;
    public $productPrice;
    public $productDescription;

    public $rules = [
        'productName' => 'required|min:2|max:48',
        'productPrice' => 'required|numeric',
        'productDescription' => 'nullable'
    ];

    public $listeners = [ 'updateProducts' => '$refresh' ];

    public function searchProduct() {
        $this->resetPage();
    }

    public function _previousPage($disabled) {
        if (!$disabled) $this->previousPage();
    }

    public function _nextPage($disabled) {
        if (!$disabled) $this->nextPage();
    }

    public function createProduct() {
        $this->validate();

        Product::create([
            'name' => $this->productName,
            'description' => $this->productDescription,
            'price' => $this->productPrice,
            'amount' => 0
        ]);

        $this->q = null;
        $this->isCreating = false;
        $this->productName = null;
        $this->productPrice = null;
        $this->productDescription = null;
    }

    public function render()
    {
        return view('livewire.admin.products.crud', [
            'products' => Product::search($this->q)->get()
                ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                ->paginate(6)->withQueryString()
        ]);
    }
}
