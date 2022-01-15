<?php

namespace App\Http\Livewire\Posts;

use Livewire\Component;

class Item extends Component
{
    public $post;
    public $standalone = false;

    public function render()
    {
        return view('livewire.posts.item');
    }
}
