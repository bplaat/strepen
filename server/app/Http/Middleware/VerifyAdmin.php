<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class VerifyAdmin
{
    public function handle($request, $next)
    {
        // Verify if the authed user is an admin
        if (Auth::user()->admin) {
            return $next($request);
        }

        abort(403);
    }
}
