<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PagesController extends Controller
{
    public function home()
    {
        $latestPosts = Post::orderBy('created_at', 'desc')->limit(5)->get();
        return view('home', ['latestPosts' => $latestPosts]);
    }
}
