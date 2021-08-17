<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class AdminProductsController extends Controller
{
    public function index()
    {
        return view('admin.products.index');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', ['product' => $product]);
    }
}
