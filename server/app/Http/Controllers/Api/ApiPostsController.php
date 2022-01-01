<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class ApiPostsController extends ApiController
{
    // Api posts index route
    public function index(Request $request)
    {
        $posts = $this->getItems(Post::class, Post::select(), $request)
            ->with('user')
            ->orderBy('created_at', 'DESC')
            ->paginate($this->getLimit($request))->withQueryString();
        return PostResource::collection($posts);
    }

    // Api posts show route
    public function show(Post $post)
    {
        $post->user;
        return new PostResource($post);
    }
}
