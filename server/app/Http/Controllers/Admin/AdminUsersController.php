<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminUsersController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function show(User $user)
    {
        return view('admin.users.show', ['user' => $user]);
    }
}
