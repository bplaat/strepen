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
    public $avatar;
    public $thanks;
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
        'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        'thanks' => 'nullable|image|mimes:gif|max:2048',
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
        $this->avatar = null;
        $this->thanks = null;
        $this->isCreating = false;
    }

    public function createUser()
    {
        $this->validate();

        $this->user->password = Hash::make($this->user->password);
        unset($this->user->password_confirmation);

        if ($this->avatar != null) {
            $avatarName = User::generateAvatarName($this->avatar->extension());
            $this->avatar->storeAs('public/avatars', $avatarName);
            $this->user->avatar = $avatarName;
        }

        if ($this->thanks != null) {
            $thanksName = User::generateThanksName($this->thanks->extension());
            $this->thanks->storeAs('public/thanks', $thanksName);
            $this->user->thanks = $thanksName;
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
            'users' => User::search(User::select(), $this->query)
                ->orderByRaw('active DESC, LOWER(IF(lastname != \'\', IF(insertion != NULL, CONCAT(lastname, \', \', insertion, \' \', firstname), CONCAT(lastname, \' \', firstname)), firstname))')
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/users.crud.title'), 'chartjs' => true]);
    }
}
