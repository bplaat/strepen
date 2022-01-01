<?php

namespace App\Http\Livewire\Admin\Posts;

use App\Models\User;
use Livewire\Component;

class Item extends Component
{
    public $post;
    public $createdAtDate;
    public $createdAtTime;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'post.user_id' => 'required|integer|exists:users,id',
        'post.title' => 'required|min:2|max:128',
        'createdAtDate' => 'required|date_format:Y-m-d',
        'createdAtTime' => 'required|date_format:H:i:s',
        'post.body' => 'required|min:2'
    ];

    public $listeners = ['inputValue'];

    public function mount()
    {
        $this->createdAtDate = $this->post->created_at->format('Y-m-d');
        $this->createdAtTime = $this->post->created_at->format('H:i:s');
    }

    public function inputValue($name, $value)
    {
        if ($name == 'item_user') {
            $this->post->user_id = $value;
        }
    }

    public function editPost()
    {
        $this->emit('inputValidate', 'item_user');
        $this->validate();

        $this->post->created_at = $this->createdAtDate . ' ' . $this->createdAtTime;
        $this->post->save();

        $this->isEditing = false;
        $this->emitUp('refresh');
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
