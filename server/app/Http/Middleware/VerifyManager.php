<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifyManager
{
    public function handle($request, $next)
    {
        // Verify if the authed user is a manager or an admin
        if (Auth::check() && (Auth::user()->role == User::ROLE_MANAGER || Auth::user()->role == User::ROLE_ADMIN)) {
            return $next($request);
        }

        abort(403);
    }
}
