<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiTransactionsController extends ApiController
{
    // Api transactions index route
    public function index(Request $request)
    {
        $transactions = $this->getItems(Transaction::class, Transaction::select(), $request)
            ->orderBy('created_at', 'DESC')
            ->paginate($this->getLimit($request))->withQueryString();
        for ($i = 0; $i < $transactions->count(); $i++) {
            $transactions[$i] = $transactions[$i]->toApiData($request->user(), ['user', 'products']);
        }
        return $transactions;
    }

    // Api transactions show route
    public function show(Request $request, Transaction $transaction)
    {
        return $transaction->toApiData($request->user(), ['user', 'products']);
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
        if (($request->user()->role == User::ROLE_MANAGER || $request->user()->role == User::ROLE_ADMIN) && $request->has('user_id')) {
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
        if (($request->user()->role == User::ROLE_MANAGER || $request->user()->role == User::ROLE_ADMIN) && $request->has('user_id')) {
            $transaction->user_id = $request->input('user_id');
        } else {
            $transaction->user_id = $request->user()->id;
        }
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
        return [
            'message' => 'Your transaction is successfully created',
            'transaction' => $transaction->toApiData($request->user(), [
                'user', // For backwards compatability
                'products'
            ])
        ];
    }
}
