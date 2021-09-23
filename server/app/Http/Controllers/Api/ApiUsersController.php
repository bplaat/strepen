<?php

namespace App\Http\Controllers\Api;

use App\Models\Inventory;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Parsedown;

class ApiUsersController extends Controller
{
    // Api users index route
    public function index(Request $request)
    {
        $searchQuery = $request->input('query');
        if ($searchQuery != '') {
            $users = User::search(User::select(), $searchQuery);
        } else {
            $users = User::where('deleted', false);
        }

        $limit = $request->input('limit');
        if ($limit != '') {
            $limit = (int)$limit;
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        $users = $users->orderByRaw('active DESC, LOWER(IF(lastname != \'\', IF(insertion != NULL, CONCAT(lastname, \', \', insertion, \' \', firstname), CONCAT(lastname, \' \', firstname)), firstname))')
            ->paginate($limit)->withQueryString();
        foreach ($users as $user) {
            $user->forApi($request->user());
        }
        return $users;
    }

    // Api users show route
    public function show(Request $request, User $user)
    {
        $user->forApi($request->user());
        return $user;
    }

    // Api users show notifcations route
    public function showNotifications(Request $request, User $user)
    {
        $limit = $request->input('limit');
        if ($limit != '') {
            $limit = (int)$limit;
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        $notifications = $request->user()->notifications()->paginate($limit)->withQueryString();
        foreach ($notifications as $notification) {
            Notification::forApi($notification, $request->user());
        }
        return $notifications;
    }

    // Api users show unread notifcations route
    public function showUnreadNotifications(Request $request, User $user)
    {
        $limit = $request->input('limit');
        if ($limit != '') {
            $limit = (int)$limit;
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        $notifications = $request->user()->unreadNotifications()->paginate($limit)->withQueryString();
        foreach ($notifications as $notification) {
            Notification::forApi($notification, $request->user());
        }
        return $notifications;
    }

    // Api users show posts route
    public function showPosts(Request $request, User $user)
    {
        $searchQuery = $request->input('query');
        if ($searchQuery != '') {
            $posts = Post::search($user->posts(), $searchQuery);
        } else {
            $posts = $user->posts()->where('deleted', false);
        }

        $limit = $request->input('limit');
        if ($limit != '') {
            $limit = (int)$limit;
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        $posts = $posts->paginate($limit)->withQueryString();
        $parsedown = new Parsedown();
        foreach ($posts as $post) {
            $post->forApi($request->user(), $parsedown);
        }
        return $posts;
    }

    // Api users show inventories route
    public function showInventories(Request $request, User $user)
    {
        $searchQuery = $request->input('query');
        if ($searchQuery != '') {
            $inventories = Inventory::search($user->inventories(), $searchQuery);
        } else {
            $inventories = $user->inventories()->where('deleted', false);
        }

        $limit = $request->input('limit');
        if ($limit != '') {
            $limit = (int)$limit;
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        $inventories = $inventories->paginate($limit)->withQueryString();
        foreach ($inventories as $inventory) {
            $inventory->forApi($request->user());
        }
        return $inventories;
    }

    // Api users show transactions route
    public function showTransactions(Request $request, User $user)
    {
        $searchQuery = $request->input('query');
        if ($searchQuery != '') {
            $transactions = Transaction::search($user->transactions(), $searchQuery);
        } else {
            $transactions = $user->transactions()->where('deleted', false);
        }

        $limit = $request->input('limit');
        if ($limit != '') {
            $limit = (int)$limit;
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        $transactions = $transactions->paginate($limit)->withQueryString();
        foreach ($transactions as $transaction) {
            $transaction->forApi($request->user());
        }
        return $transactions;
    }

    // Api users check balances route
    public function checkBalances()
    {
        User::checkBalances();
        return [
            'message' => 'User balances are checked'
        ];
    }
}
