<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email;
    public $password;

    public $rules = [
        'email' => 'required|email',
        'password' => 'required'
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], true)) {
            return redirect()->route('home');
        }

        $this->addError('email', __('auth.login.error_message'));
        $this->addError('password', 'null');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
