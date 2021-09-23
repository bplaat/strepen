<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Parsedown;

class ApiPostsController extends Controller
{
    // Api posts index route
    public function index(Request $request)
    {
        $searchQuery = $request->input('query');
        if ($searchQuery != '') {
            $posts = Post::search(Post::select(), $searchQuery);
        } else {
            $posts = Post::where('deleted', false);
        }

        $limit = $request->input('limit');
        if ($limit != '') {
            $limit = (int)$limit;
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        $posts = $posts->orderBy('created_at', 'DESC')->paginate($limit)->withQueryString();
        $parsedown = new Parsedown();
        foreach ($posts as $post) {
            $post->forApi($request->user(), $parsedown);
        }
        return $posts;
    }

    // Api posts show route
    public function show(Request $request, Post $post)
    {
        // Load user of this post
        $post->user->forApi($request->user());
        return $post;
    }
}
