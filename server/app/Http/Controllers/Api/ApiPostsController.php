<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Helpers\ApiUtils;
use Illuminate\Http\Request;

class ApiPostsController extends Controller
{
    // Api posts index route
    public function index(Request $request)
    {
        $posts = Post::search(Post::select(), $request->input('query'))
            ->with('user')
            ->orderBy('created_at', 'DESC')
            ->paginate(ApiUtils::parseLimit($request))->withQueryString();
        return PostResource::collection($posts);
    }

    // Api posts show route
    public function show(Post $post)
    {
        $post->user;
        return new PostResource($post);
    }

    // Api posts like route
    public function like(Request $request, Post $post)
    {
        $post->like($request->user());
        unset($post->likes);
        return new PostResource($post);
    }

    // Api posts dislike route
    public function dislike(Request $request, Post $post)
    {
        $post->dislike($request->user());
        unset($post->dislikes);
        return new PostResource($post);
    }
}
