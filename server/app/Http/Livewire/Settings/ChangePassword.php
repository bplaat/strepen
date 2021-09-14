<?php

namespace App\Http\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangePassword extends Component
{
    public $currentPassword;
    public $password;
    public $passwordConfirmation;

    public $rules = [
        'currentPassword' => 'required|current_password',
        'password' => 'required|min:6',
        'passwordConfirmation' => 'required|same:password'
    ];

    public function mount()
    {
        $this->currentPassword = null;
        $this->password = null;
        $this->passwordConfirmation = null;
    }

    public function changePassword()
    {
        $this->validate();

        Auth::user()->update([
            'password' => Hash::make($this->password)
        ]);

        session()->flash('change_password_message', __('settings.change_password.success_message'));
        $this->mount();
    }

    public function render()
    {
        return view('livewire.settings.change-password');
    }
}
