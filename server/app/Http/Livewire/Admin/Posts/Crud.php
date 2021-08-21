<?php

namespace App\Http\Livewire\Admin\Posts;

use App\Http\Livewire\PaginationComponent;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class Crud extends PaginationComponent
{
    public $post;
    public $isCreating = false;

    public $rules = [
        'post.title' => 'required|min:2|max:48',
        'post.body' => 'required|min:2'
    ];

    public function mount()
    {
        $this->post = new Post();
    }

    public function createPost()
    {
        $this->validate();
        $this->post->user_id = Auth::id();
        $this->post->save();
        $this->reset();
    }

    public function render()
    {
        return view('livewire.admin.posts.crud', [
            'posts' => Post::search($this->q)->get()
                ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.livewire', ['title' => __('admin/posts.crud.title')]);
    }
}
