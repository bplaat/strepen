<?php

namespace App\Http\Livewire\Components;

use App\Models\User;
use Livewire\Component;

class UserChooser extends Component
{
    public $userId;
    public $includeStrepenUser = false;

    public $users;
    public $filteredUsers;
    public $userName;
    public $user;
    public $isOpen = false;

    public function mount()
    {
        if ($this->includeStrepenUser) {
            $this->users = User::where('deleted', false)->where(function ($query) {
                return $query->where('active', true)->orWhere('id', 1);
            })->orderBy('balance', 'DESC')->get();
        } else {
            $this->users = User::where('active', true)->where('deleted', false)
                ->orderBy('balance', 'DESC')->get();
        }
        $this->filteredUsers = $this->users->slice(0, 10);

        if ($this->userId != null) {
            $this->selectUser($this->userId);
        }
    }

    public function updatedUserName() {
        if (!$this->isOpen) {
            $this->isOpen = true;
        }

        if ($this->user != null && $this->userName != $this->user->name) {
            $this->user = null;
            $this->emitUp('userChooser', null);
        }

        $this->filteredUsers = $this->users->filter(function ($user) {
            return strlen($this->userName) == 0 || stripos($user->name, $this->userName) !== false;
        })->slice(0, 10);
    }

    public function selectFirstUser() {
        if ($this->filteredUsers->count() > 0) {
            $this->user = $this->filteredUsers->first();
            $this->emitUp('userChooser', $this->user->id);
            $this->userName = $this->user->name;
            $this->updatedUserName();
            $this->isOpen = false;
        }
    }

    public function selectUser($userId) {
        $this->user = $this->users->firstWhere('id', $userId);
        $this->emitUp('userChooser', $this->user->id);
        $this->userName = $this->user->name;
        $this->updatedUserName();
    }

    public function render()
    {
        return view('livewire.components.user-chooser');
    }
}