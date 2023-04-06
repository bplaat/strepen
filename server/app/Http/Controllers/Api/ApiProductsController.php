<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Helpers\ApiUtils;
use Illuminate\Http\Request;

class ApiProductsController extends Controller
{
    // Api products index route
    public function index(Request $request)
    {
        $products = Product::search(Product::select(), $request->input('query'))
            ->orderByRaw('active DESC, LOWER(name)');
        if (! $request->user()->manager) {
            $products = $products->where('active', true);
        }
        $products = $products->paginate(ApiUtils::parseLimit($request))->withQueryString();
        return ProductResource::collection($products);
    }

    // Api products index active route
    public function indexActive(Request $request)
    {
        $products = Product::search(Product::select(), $request->input('query'))
            ->orderByRaw('LOWER(name)')
            ->where('active', true)
            ->paginate(ApiUtils::parseLimit($request))->withQueryString();
        return ProductResource::collection($products);
    }

    // Api products show route
    public function show(Product $product)
    {
        return new ProductResource($product);
    }
}
