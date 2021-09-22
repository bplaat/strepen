<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use Parsedown;

class ApiPostsController extends Controller
{
    // Api posts index route
    public function index()
    {
        if (request('query') != '') {
            $posts = Post::search(Post::select(), request('query'));
        } else {
            $posts = Post::where('deleted', false);
        }
        if (request('limit') != '') {
            $limit = (int)request('limit');
            if ($limit < 1) $limit = 1;
            if ($limit > 50) $limit = 50;
        } else {
            $limit = config('pagination.api.limit');
        }

        $parsedown = new Parsedown();
        return $posts->orderBy('created_at', 'DESC')
            ->paginate($limit)
            ->map(function ($post) use ($parsedown) {
                $post->body = $parsedown->text($post->body);
                return $post;
            });
    }

    // Api posts show route
    public function show(Post $post)
    {
        $post->user;
        return $post;
    }
}
