<?php

namespace App\Http\Livewire;

use App\Http\Livewire\PaginationComponent;
use App\Models\Post;

class Home extends PaginationComponent
{
    public function render()
    {
        return view('livewire.home', [
            'posts' => Post::search($this->q)
                ->sortByDesc('created_at')
                ->paginate(config('pagination.web.small_limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('home.title')]);
    }
}