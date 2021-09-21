<?php

namespace App\Http\Livewire\Admin\Users;

use App\Http\Livewire\PaginationComponent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class Crud extends PaginationComponent
{
    use WithFileUploads;

    public $user;
    public $userAvatar;
    public $isCreating;

    public $rules = [
        'user.firstname' => 'required|min:2|max:48',
        'user.insertion' => 'nullable|max:16',
        'user.lastname' => 'required|min:2|max:48',
        'user.gender' => 'nullable|integer|digits_between:' . User::GENDER_MALE . ',' . User::GENDER_OTHER,
        'user.birthday' => 'nullable|date',
        'user.email' => 'required|email|max:255|unique:users,email',
        'user.phone' => 'nullable|max:255',
        'user.address' => 'nullable|min:2|max:255',
        'user.postcode' => 'nullable|min:2|max:32',
        'user.city' => 'nullable|min:2|max:255',
        'user.password' => 'required|min:6',
        'user.password_confirmation' => 'required|same:user.password_confirmation',
        'userAvatar' => 'nullable|image|max:1024',
        'user.role' => 'required|integer|digits_between:' . User::ROLE_NORMAL . ',' . User::ROLE_ADMIN,
        'user.language' => 'required|integer|digits_between:' . User::LANGUAGE_ENGLISH . ',' . User::LANGUAGE_DUTCH,
        'user.theme' => 'required|integer|digits_between:' . User::THEME_LIGHT . ',' . User::THEME_DARK,
        'user.receive_news' => 'nullable|boolean'
    ];

    public function mount()
    {
        $this->user = new User();
        $this->user->role = User::ROLE_NORMAL;
        $this->user->language = User::LANGUAGE_DUTCH;
        $this->user->theme = User::THEME_LIGHT;
        $this->user->receive_news = true;
        $this->userAvatar = null;
        $this->isCreating = false;
    }

    public function createUser()
    {
        $this->validate();

        $this->user->password = Hash::make($this->user->password);
        unset($this->user->password_confirmation);

        if ($this->userAvatar != null) {
            $avatarName = User::generateAvatarName($this->userAvatar->extension());
            $this->userAvatar->storeAs('public/avatars', $avatarName);
            $this->user->avatar = $avatarName;
        }

        $this->user->balance = 0;
        $this->user->save();
        $this->mount();
    }

    public function checkBalances()
    {
        User::checkBalances();
    }

    public function render()
    {
        return view('livewire.admin.users.crud', [
            'users' => User::search(User::select(), $this->query)->get()
                ->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE)
                ->sortByDesc('active')
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/users.crud.title'), 'chartjs' => true]);
    }
}
