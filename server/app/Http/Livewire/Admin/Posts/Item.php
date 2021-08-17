<?php

namespace App\Http\Livewire\Admin\Posts;

use Livewire\Component;

class Item extends Component
{
    public $post;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'post.title' => 'required|min:2|max:48',
        'post.body' => 'required|min:2'
    ];

    public function updatePost() {
        $this->validate();
        $this->isEditing = false;
        $this->post->save();
    }

    public function deletePost() {
        $this->isDeleting = false;
        $this->post->delete();
        $this->emitUp('updatePosts');
    }

    public function render()
    {
        return view('livewire.admin.posts.item');
    }
}
