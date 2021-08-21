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
}
