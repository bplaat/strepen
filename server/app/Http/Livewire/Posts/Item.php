<?php

namespace App\Http\Livewire\Posts;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Item extends Component
{
    public $post;
    public $standalone = false;

    public function likePost()
    {
        $user = Auth::user();
        if ($user->id == 1) return;

        if ($this->post->likes->contains($user)) {
            $this->post->likes()->detach($user);
        } else {
            $this->post->dislikes()->detach($user);
            $this->post->likes()->attach($user);
        }
    }

    public function dislikePost()
    {
        $user = Auth::user();
        if ($user->id == 1) return;

        if ($this->post->dislikes->contains($user)) {
            $this->post->dislikes()->detach($user);
        } else {
            $this->post->likes()->detach($user);
            $this->post->dislikes()->attach($user);
        }
    }

    public function render()
    {
        unset($this->post->likes);
        unset($this->post->dislikes);
        return view('livewire.posts.item');
    }
}
