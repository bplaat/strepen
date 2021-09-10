<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function kiosk()
    {
        // Login to the Kiosk user
        Auth::login(User::find(1), true);

        // Redirect to the home page
        return redirect()->route('home');
    }
}
