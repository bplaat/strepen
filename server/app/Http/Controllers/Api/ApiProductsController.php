<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ApiProductsController extends ApiController
{
    // Api products index route
    public function index(Request $request)
    {
        $products = $this->getItems(Product::class, Product::select(), $request)
            ->orderByRaw('active DESC, LOWER(name)');
        if ($request->user()->role != User::ROLE_MANAGER && $request->user()->role != User::ROLE_ADMIN) {
            $products = $products->where('active', true);
        }
        $products = $products->paginate($this->getLimit($request))->withQueryString();
        for ($i = 0; $i < $products->count(); $i++) {
            $products[$i] = $products[$i]->toApiData($request->user());
        }
        return $products;
    }

    // Api products show route
    public function show(Request $request, Product $product)
    {
        return $product->toApiData($request->user());
    }
}
