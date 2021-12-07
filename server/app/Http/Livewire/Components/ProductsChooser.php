<?php

namespace App\Http\Livewire\Components;

use App\Models\Product;
use App\Models\Setting;

class ProductsChooser extends InputComponent
{
    // Props
    public $initialProducts = [];
    public $noMax = false;
    public $minor = false;
    public $bigMode = false;
    public $includeInactive = false;

    // State
    public $products;
    public $filteredProducts;
    public $productName;
    public $selectedProducts;
    public $isOpen = false;

    // Lifecycle
    public function mount()
    {
        // Select all products
        $products = Product::where('deleted', false);
        if (!$this->includeInactive) {
            $products = $products->where('active', true);
        }
        $this->products = $products->orderByRaw('LOWER(name)')->get();

        // Selected products ids
        $this->selectedProducts = collect();
        foreach ($this->initialProducts as $product) {
            $selectedProduct = [];
            $selectedProduct['product_id'] = $product->id;
            $selectedProduct['amount'] = $product->pivot->amount;
            $this->selectedProducts->push($selectedProduct);
        }
        $this->sortSelectedProducts();
        $this->filterProducts();
    }

    public function sortSelectedProducts()
    {
        $this->selectedProducts = $this->selectedProducts->sort(function ($a, $b) {
            $productA = $this->products->firstWhere('id', $a['product_id']);
            $productB = $this->products->firstWhere('id', $b['product_id']);
            return strcasecmp($productA->name, $productB->name);
        })->values();
    }

    public function filterProducts()
    {
        $filteredProducts = $this->products;
        if (!$this->bigMode) {
            $filteredProducts = $filteredProducts->filter(fn ($product) =>
                !$this->selectedProducts->pluck('product_id')->contains($product->id) &&
                (strlen($this->productName) == 0 || stripos($product->name, $this->productName) !== false)
            );
        }
        if ($this->minor) {
            $filteredProducts = $filteredProducts->filter(fn ($product) => !$product->alcoholic);
        }
        if (!$this->bigMode) {
            $filteredProducts = $filteredProducts->slice(0, 10);
        }
        $this->filteredProducts = $filteredProducts;
    }

    public function emitValue()
    {
        $this->emitUp('inputValue', $this->name, $this->selectedProducts
            ->filter(fn ($selectedProduct) => $selectedProduct['amount'] > 0)
            ->toArray());
    }

    public function render()
    {
        return view('livewire.components.products-chooser');
    }

    // Events
    public function inputValidate($name)
    {
        if ($this->name == $name) {
            $this->valid = $this->selectedProducts
                ->filter(fn ($selectedProduct) => $selectedProduct['amount'] > 0)
                ->count() > 0;
        }
    }

    public function inputClear($name)
    {
        if ($this->name == $name) {
            $this->productName = '';
            $this->mount();
            $this->isOpen = false;
        }
    }

    public function inputProps($name, $props)
    {
        if ($this->name == $name) {
            $this->minor = $props['minor'];

            if ($this->minor) {
                $this->selectedProducts = $this->selectedProducts
                    ->filter(fn ($selectedProduct) => !$this->products->firstWhere('id', $selectedProduct['product_id'])->alcoholic);
                $this->emitValue();
            }

            $this->filterProducts();
        }
    }

    // Listeners
    public function updatedProductName()
    {
        $this->isOpen = true;
        $this->filterProducts();
    }

    public function updatedSelectedProducts()
    {
        $this->emitValue();
    }

    // Actions
    public function addFirstProduct()
    {
        if ($this->filteredProducts->count() > 0) {
            $this->addProduct($this->filteredProducts->first()->id);
        }
    }

    public function addProduct($productId)
    {
        $selectedProduct = [];
        $selectedProduct['product_id'] = $productId;
        $selectedProduct['amount'] = 0;
        $this->selectedProducts->push($selectedProduct);
        $this->sortSelectedProducts();
        $this->productName = null;
        $this->emitValue();
        $this->filterProducts();
        $this->isOpen = false;
    }

    public function deleteProduct($productId)
    {
        $this->selectedProducts = $this->selectedProducts->where('product_id', '!=', $productId);
        $this->emitValue();
    }

    public function decrementProductAmount($productId)
    {
        $this->selectedProducts = $this->selectedProducts->map(function ($selectedProduct) use ($productId) {
            if ($selectedProduct['product_id'] == $productId) {
                if ($selectedProduct['amount'] > 0) {
                    $selectedProduct['amount']--;
                }
            }
            return $selectedProduct;
        });
        $this->emitValue();
    }

    public function incrementProductAmount($productId)
    {
        $selectedProduct = $this->selectedProducts->firstWhere('product_id', $productId);
        if ($selectedProduct != null) {
            $this->selectedProducts = $this->selectedProducts->map(function ($selectedProduct) use ($productId) {
                if ($selectedProduct['product_id'] == $productId) {
                    if ($this->noMax || $selectedProduct['amount'] < Setting::get('max_stripe_amount')) {
                        $selectedProduct['amount']++;
                    }
                }
                return $selectedProduct;
            });
        } else {
            $selectedProduct = [];
            $selectedProduct['product_id'] = $productId;
            $selectedProduct['amount'] = 1;
            $this->selectedProducts->push($selectedProduct);
        }
        $this->emitValue();
    }
}
