<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
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
            ->paginate($limit);
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

        $posts = $posts->paginate($limit);
        $parsedown = new Parsedown();
        foreach ($posts as $post) {
            $post->forApi($request->user(), $parsedown);
        }
        return $posts;
    }
}
