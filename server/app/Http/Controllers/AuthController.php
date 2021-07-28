<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Session;

class AuthController extends Controller
{
    public function logout()
    {
        // Logout user
        Session::flush();
        Auth::logout();

        // Go to login page
        return redirect()->route('auth.login');
    }
}
