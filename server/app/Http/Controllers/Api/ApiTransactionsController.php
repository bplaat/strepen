<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            $limit = 20;
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

    // Api transactions store route
    public function store(Request $request)
    {
        // Validate input
        $rules = [
            'name' => 'required|min:2|max:48',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.amount' => 'required|integer|min:1'
        ];
        if (($request->user()->role == User::ROLE_MANAGER || $request->user()->role == User::ROLE_ADMIN) && $request->input('user_id')) {
            $rules['user_id'] = 'required|integer|exists:users,id';
        }
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response(['errors' => $validation->errors()], 400);
        }

        $productsData = $request->input('products', []);
        if (count($productsData) == 0) {
            return response(['errors' => [
                'products' => 'You need to add minimal one product to the transaction'
            ]], 400);
        }

        // Create transaction
        $transaction = new Transaction();
        $transaction->user_id = $request->input('user_id') ?? $request->user()->id;
        $transaction->type = Transaction::TYPE_TRANSACTION;
        $transaction->name = $request->input('name');
        $transaction->price = 0;
        $transaction->save();

        // Attach products to transaction
        foreach ($productsData as $productData) {
            $product = Product::find($productData['product_id']);
            $transaction->price += $product->price * $productData['amount'];
            $transaction->products()->attach($product->id, [ 'amount' => $productData['amount'] ]);

            $product->amount -= $productData['amount'];
            $product->save();
        }
        $transaction->save();

        // Update user balance
        $user = $request->user();
        $user->balance -= $transaction->price;
        $user->save();

        // Return success message
        $transaction->forApi($request->user());
        return [
            'message' => 'Your transaction is successfully created',
            'transaction' => $transaction
        ];
    }
}
