<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventories;

class AdminInventoriesController extends Controller
{
    public function index()
    {
        return view('admin.inventories.index');
    }

    public function show(Inventories $inventories)
    {
        return view('admin.inventories.show', ['inventories' => $inventories]);
    }
}
