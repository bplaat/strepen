<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;

class ApiUsersController extends Controller
{
    // Api users index route
    public function index()
    {
        if (request('query') != '') {
            $users = User::search(User::select(), request('query'));
        } else {
            $users = User::where('deleted', false);
        }
        if (request('limit') != '') {
            $limit = (int)request('limit');
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        return $users //->get()
            // ->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE)
            // ->sortByDesc('active')
            ->paginate($limit);
    }

    // Api users show route
    public function show(User $user)
    {
        $user->posts;
        return $user;
    }
}
