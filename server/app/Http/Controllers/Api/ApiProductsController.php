<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Parsedown;

class ApiProductsController extends Controller
{
    // Api products index route
    public function index(Request $request)
    {
        $searchQuery = $request->input('query');
        if ($searchQuery != '') {
            $products = Product::search(Product::select(), $searchQuery);
        } else {
            $products = Product::where('deleted', false);
        }

        $limit = $request->input('limit');
        if ($limit != '') {
            $limit = (int)$limit;
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        if ($request->user() == null || $request->user()->role != User::ROLE_ADMIN) {
            $products = $products->where('active', true);
        }

        $products = $products->orderByRaw('active DESC, LOWER(name)')->paginate($limit)->withQueryString();
        foreach ($products as $product) {
            $product->forApi($request->user());
        }
        return $products;
    }

    // Api products show route
    public function show(Request $request, Product $product)
    {
        $product->forApi($request->user());
        return $product;
    }
}
