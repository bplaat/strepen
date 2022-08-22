<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class VerifyKiosk
{
    public function handle($request, $next)
    {
        // Verify if the authed user is the kiosk user
        if (Auth::id() == 1) {
            return $next($request);
        }

        abort(403);
    }
}
