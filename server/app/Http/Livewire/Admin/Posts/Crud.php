<?php

namespace App\Http\Livewire\Admin\Posts;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Crud extends Component
{
    use WithPagination;

    public function paginationView()
    {
        return 'pagination';
    }

    public $q;
    public $queryString = ['q'];

    public $isCreating = false;
    public $postTitle;
    public $postBody;

    public $rules = [
        'postTitle' => 'required|min:2|max:48',
        'postBody' => 'required|min:2'
    ];

    public $listeners = [ 'refresh' => '$refresh' ];

    public function searchPost()
    {
        $this->resetPage();
    }

    public function _previousPage($disabled)
    {
        if (!$disabled) $this->previousPage();
    }

    public function _nextPage($disabled)
    {
        if (!$disabled) $this->nextPage();
    }

    public function createPost()
    {
        $this->validate();

        Post::create([
            'user_id' => Auth::id(),
            'title' => $this->postTitle,
            'body' => $this->postBody
        ]);

        $this->q = null;
        $this->isCreating = false;
        $this->postTitle = null;
        $this->postBody = null;
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
