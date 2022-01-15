<?php

namespace App\Http\Livewire\Posts;

use App\Models\Post;
use Livewire\Component;

class Show extends Component
{
    public $post;

    public function mount(Post $post)
    {
        $this->post = $post;
    }

    public function render()
    {
        return view('livewire.posts.show')->layout('layouts.app', ['title' => __('posts.show.title', ['post.title' => $this->post->title])]);
    }
}
