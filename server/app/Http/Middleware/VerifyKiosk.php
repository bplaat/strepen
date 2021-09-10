<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifyKiosk
{
    public function handle($request, $next)
    {
        // Verify if the authed user is the kiosk user
        if (!Auth::check() || Auth::id() != 1) {
            abort(403);
        }

        return $next($request);
    }
}
