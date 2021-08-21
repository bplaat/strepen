<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Item extends Component
{
    public $user;
    public $isEditing = false;
    public $isDeleting = false;

    public function rules()
    {
        return [
            'user.firstname' => 'required|min:2|max:48',
            'user.insertion' => 'nullable|max:16',
            'user.lastname' => 'required|min:2|max:48',
            // TODO: avatar
            'user.gender' => 'nullable|integer|digits_between:' . User::GENDER_MALE . ',' . User::GENDER_OTHER,
            'user.birthday' => 'nullable|date',
            'user.email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->email, 'email')
            ],
            'user.phone' => 'nullable|max:255',
            'user.address' => 'nullable|min:2|max:255',
            'user.postcode' => 'nullable|min:2|max:32',
            'user.city' => 'nullable|min:2|max:255',
            // TODO: password
            'user.role' => 'required|integer|digits_between:' . User::ROLE_NORMAL . ',' . User::ROLE_ADMIN
        ];
    }


    public function editUser()
    {
        $this->validate();
        $this->isEditing = false;
        if ($this->user->gender == '') $this->user->gender = null;
        if ($this->user->birthday == '') $this->user->birthday = null;
        $this->user->save();
    }

    public function deleteUser()
    {
        $this->isDeleting = false;
        $this->user->delete();
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.users.item');
    }
}
