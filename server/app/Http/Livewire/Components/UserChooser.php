<?php

namespace App\Http\Livewire\Components;

use App\Models\User;

class UserChooser extends InputComponent
{
    // Props
    public $userId;
    public $inline = false;
    public $relationship = false;
    public $includeInactive = false;
    public $includeStrepenUser = false;
    public $postsRequired = false;
    public $inventoriesRequired = false;

    // State
    public $users;
    public $filteredUsers;
    public $userName;
    public $user;
    public $isOpen = false;

    // Lifecycle
    public function mount()
    {
        $users = User::where('deleted', false);
        if (!$this->includeInactive) {
            if ($this->includeStrepenUser) {
                $users = $users->where(fn ($query) => $query->where('active', true)->orWhere('id', 1));
            } else {
                $users = $users->where('active', true);
            }
        }
        $this->users = $users->orderBy('balance', 'DESC')->withCount([
            'posts' => function ($query) {
                return $query->where('deleted', false);
            },
            'inventories' => function ($query) {
                return $query->where('deleted', false);
            }
        ])->get();

        if ($this->postsRequired) {
            $this->users = $this->users->filter(fn ($user) => $user->posts_count > 0);
        }
        if ($this->inventoriesRequired) {
            $this->users = $this->users->filter(fn ($user) => $user->inventories_count > 0);
        }
        $this->filterUsers();

        if ($this->userId != null) {
            $this->selectUser($this->userId);
        }
    }

    public function filterUsers()
    {
        $this->filteredUsers = $this->users
            ->filter(fn ($user) => strlen($this->userName) == 0 || stripos($user->name, $this->userName) !== false)
            ->slice(0, 10);
    }

    public function emitValue()
    {
        $this->emitUp('inputValue', $this->name, $this->user != null ? $this->user->id : null);
    }

    public function render()
    {
        return view('livewire.components.user-chooser');
    }

    // Events
    public function inputValidate($name)
    {
        if ($this->name == $name) {
            $this->valid = $this->user != null;
        }
    }

    public function inputClear($name)
    {
        if ($this->name == $name) {
            $this->userName = '';
            $this->user = null;
            $this->emitValue();
            $this->filterUsers();
            $this->isOpen = false;
        }
    }

    // Listeners
    public function updatedUserName()
    {
        $this->isOpen = true;
        if ($this->user != null && $this->userName != $this->user->name) {
            $this->user = null;
            $this->emitValue();
        }
        $this->filterUsers();
    }

    // Actions
    public function selectFirstUser()
    {
        if ($this->filteredUsers->count() > 0) {
            $this->selectUser($this->filteredUsers->first()->id);
        }
    }

    public function selectUser($userId)
    {
        $this->user = $this->users->firstWhere('id', $userId);
        $this->userName = $this->user->name;
        $this->emitValue();
        $this->filterUsers();
        $this->isOpen = false;
    }
}
