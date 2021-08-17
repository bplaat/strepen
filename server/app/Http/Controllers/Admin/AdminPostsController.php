<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;

class AdminPostsController extends Controller
{
    public function index()
    {
        return view('admin.posts.index');
    }

    public function show(Post $post)
    {
        return view('admin.posts.show', ['post' => $post]);
    }
}
