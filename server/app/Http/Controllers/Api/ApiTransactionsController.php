<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiTransactionsController extends Controller
{
    // Api transactions index route
    public function index(Request $request)
    {
        $searchQuery = $request->input('query');
        if ($searchQuery != '') {
            $transactions = Transaction::search(Transaction::select(), $searchQuery);
        } else {
            $transactions = Transaction::where('deleted', false);
        }

        $limit = $request->input('limit');
        if ($limit != '') {
            $limit = (int)$limit;
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        $transactions = $transactions->orderBy('created_at', 'DESC')->paginate($limit)->withQueryString();
        foreach ($transactions as $transaction) {
            $transaction->forApi($request->user());
        }
        return $transactions;
    }

    // Api transactions show route
    public function show(Request $request, Transaction $transaction)
    {
        $transaction->forApi($request->user());
        return $transaction;
    }
}
