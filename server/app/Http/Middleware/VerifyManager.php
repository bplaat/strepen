<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class VerifyManager
{
    public function handle($request, $next)
    {
        // Verify if the authed user is at least a manager
        if (Auth::user()->manager) {
            return $next($request);
        }

        abort(403);
    }
}
