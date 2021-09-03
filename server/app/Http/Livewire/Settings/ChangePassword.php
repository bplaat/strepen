<?php

namespace App\Http\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangePassword extends Component
{
    public $current_password;
    public $password;
    public $password_confirmation;

    public $rules = [
        'current_password' => 'required|current_password',
        'password' => 'required|min:6|confirmed'
    ];

    public function mount()
    {
        $this->current_password = null;
        $this->password = null;
        $this->password_confirmation = null;
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
