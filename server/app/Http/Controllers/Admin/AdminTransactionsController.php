<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class AdminTransactionsController extends Controller
{
    public function index()
    {
        return view('admin.transactions.index');
    }
}
