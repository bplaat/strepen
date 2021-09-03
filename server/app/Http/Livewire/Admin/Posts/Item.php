<?php

namespace App\Http\Livewire\Admin\Posts;

use App\Models\User;
use Livewire\Component;

class Item extends Component
{
    public $post;
    public $users;
    public $postCreatedAtDate;
    public $postCreatedAtTime;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'post.user_id' => 'required|integer|exists:users,id',
        'post.title' => 'required|min:2|max:48',
        'postCreatedAtDate' => 'required|date_format:Y-m-d',
        'postCreatedAtTime' => 'required|date_format:H:i:s',
        'post.body' => 'required|min:2'
    ];

    public function mount()
    {
        $this->users = User::all()->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE);
        $this->postCreatedAtDate = $this->post->created_at->format('Y-m-d');
        $this->postCreatedAtTime = $this->post->created_at->format('H:i:s');
    }

    public function editPost()
    {
        $this->validate();
        $this->post->created_at = $this->postCreatedAtDate . ' ' . $this->postCreatedAtTime;
        $this->post->save();
        $this->isEditing = false;
    }

    public function deletePost()
    {
        $this->isDeleting = false;
        $this->post->delete();
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.posts.item');
    }
}
