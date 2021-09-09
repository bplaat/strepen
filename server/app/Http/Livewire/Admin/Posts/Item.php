<?php

namespace App\Http\Livewire\Admin\Posts;

use App\Models\User;
use Livewire\Component;

class Item extends Component
{
    public $users;
    public $post;
    public $createdAtDate;
    public $createdAtTime;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'post.user_id' => 'required|integer|exists:users,id',
        'post.title' => 'required|min:2|max:48',
        'createdAtDate' => 'required|date_format:Y-m-d',
        'createdAtTime' => 'required|date_format:H:i:s',
        'post.body' => 'required|min:2'
    ];

    public function mount()
    {
        $this->users = User::all()->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE);
        $this->createdAtDate = $this->post->created_at->format('Y-m-d');
        $this->createdAtTime = $this->post->created_at->format('H:i:s');
    }

    public function editPost()
    {
        $this->validate();
        $this->post->created_at = $this->createdAtDate . ' ' . $this->createdAtTime;
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
        unset($this->post->user);
        return view('livewire.admin.posts.item');
    }
}
