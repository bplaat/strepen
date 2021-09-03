<?php

namespace App\Http\Livewire\Admin\Users;

use App\Http\Livewire\PaginationComponent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Crud extends PaginationComponent
{
    public $user;
    public $isCreating;

    public $rules = [
        'user.firstname' => 'required|min:2|max:48',
        'user.insertion' => 'nullable|max:16',
        'user.lastname' => 'required|min:2|max:48',
        // TODO: avatar
        'user.gender' => 'nullable|integer|digits_between:' . User::GENDER_MALE . ',' . User::GENDER_OTHER,
        'user.birthday' => 'nullable|date',
        'user.email' => 'required|email|max:255|unique:users,email',
        'user.phone' => 'nullable|max:255',
        'user.address' => 'nullable|min:2|max:255',
        'user.postcode' => 'nullable|min:2|max:32',
        'user.city' => 'nullable|min:2|max:255',
        'user.password' => 'required|min:6',
        'user.password_confirmation' => 'required|same:user.password_confirmation',
        'user.role' => 'nullable|integer|digits_between:' . User::ROLE_NORMAL . ',' . User::ROLE_ADMIN
    ];

    public function mount()
    {
        $this->user = new User();
        $this->isCreating = false;
    }

    public function createUser()
    {
        $this->validate();
        $this->user->password = Hash::make($this->user->password);
        unset($this->user->password_confirmation);
        if ($this->user->role == '') $this->user->role = User::ROLE_NORMAL;
        $this->user->money = 0;
        $this->user->save();
        $this->mount();
    }

    public function render()
    {
        return view('livewire.admin.users.crud', [
            'users' => User::search($this->q)->get()
                ->sortBy('sortName', SORT_NATURAL | SORT_FLAG_CASE)
                ->paginate(config('pagination.web.limit'))->withQueryString()
        ])->layout('layouts.livewire', ['title' => __('admin/users.crud.title')]);
    }
}
