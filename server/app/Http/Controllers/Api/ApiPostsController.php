<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;

class ApiPostsController extends ApiController
{
    // Api posts index route
    public function index(Request $request)
    {
        $posts = $this->getItems(Post::class, Post::select(), $request)
            ->orderBy('created_at', 'DESC')
            ->paginate($this->getLimit($request))->withQueryString();
        for ($i = 0; $i < $posts->count(); $i++) {
            $posts[$i] = $posts[$i]->toApiData($request->user(), ['user']);
        }
        return $posts;
    }

    // Api posts show route
    public function show(Request $request, Post $post)
    {
        return $post->toApiData($request->user(), ['user']);
    }
}
