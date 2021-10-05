<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\User;
use \Parsedown;

class Home extends PaginationComponent
{
    public $user_id;
    public $userIdTemp;

    public function __construct() {
        parent::__construct();
        $this->queryString[] = 'user_id';
        $this->listeners[] = 'userChooser';
    }

    public function mount() {
        if ($this->user_id != 1) {
            $user = User::where('id', $this->user_id)->where('active', true)->where('deleted', false)->withCount('posts')->first();
            if ($user == null || $user->posts_count == 0) {
                $this->user_id = null;
            }
        }
    }

    public function userChooser($userId) {
        $this->userIdTemp = $userId;
    }

    public function search()
    {
        $this->user_id = $this->userIdTemp;
        $this->resetPage();
    }

    public function render()
    {
        $posts = Post::search(Post::select(), $this->query);
        if ($this->user_id != null) {
            $posts = $posts->where('user_id', $this->user_id);
        }
        return view('livewire.home', [
            'parsedown' => new Parsedown(),
            'posts' => $posts->with('user')
                ->orderBy('created_at', 'DESC')
                ->paginate(config('pagination.web.small_limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('home.title')]);
    }
}
