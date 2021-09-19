<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Item extends Component
{
    use WithFileUploads;

    public $user;
    public $newPassword;
    public $newPasswordConfirmation;
    public $avatar;
    public $isShowing = false;
    public $isEditing = false;
    public $isDeleting = false;

    public function rules()
    {
        return [
            'user.firstname' => 'required|min:2|max:48',
            'user.insertion' => 'nullable|max:16',
            'user.lastname' => 'required|min:2|max:48',
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
            'newPassword' => 'nullable|min:6',
            'newPasswordConfirmation' => $this->newPassword != null ? ['required', 'same:newPassword'] : [],
            'avatar' => 'nullable|image|max:1024',
            'user.role' => 'required|integer|digits_between:' . User::ROLE_NORMAL . ',' . User::ROLE_ADMIN,
            'user.language' => 'nullable|integer|digits_between:' . User::LANGUAGE_ENGLISH . ',' . User::LANGUAGE_DUTCH,
            'user.theme' => 'nullable|integer|digits_between:' . User::THEME_LIGHT . ',' . User::THEME_DARK,
            'user.active' => 'nullable|boolean'
        ];
    }

    public function editUser()
    {
        $this->validate();

        if ($this->user->gender == '') $this->user->gender = null;
        if ($this->user->birthday == '') $this->user->birthday = null;

        if ($this->newPassword != null) {
            $this->user->password = Hash::make($this->newPassword);
        }

        if ($this->avatar != null) {
            $avatarName = User::generateAvatarName($this->avatar->extension());
            $this->avatar->storeAs('public/avatars', $avatarName);

            if ($this->user->avatar != null) {
                Storage::delete('public/avatars/' . $this->user->avatar);
            }
            $this->user->avatar = $avatarName;
        }

        $this->isEditing = false;
        $this->user->save();
        $this->newPassword = null;
        $this->newPasswordConfirmation = null;
    }

    public function hijackUser()
    {
        Auth::login($this->user, true);
        return redirect()->route('home');
    }

    public function deleteAvatar()
    {
        if ($this->user->avatar != null) {
            Storage::delete('public/avatars/' . $this->user->avatar);
        }
        $this->user->avatar = null;
        $this->user->save();
    }

    public function deleteUser()
    {
        if ($this->user->avatar != null) {
            Storage::delete('public/avatars/' . $this->user->avatar);
        }
        $this->isDeleting = false;
        $this->user->delete();
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.users.item');
    }
}
