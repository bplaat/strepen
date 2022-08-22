<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;

class VerifyNoKiosk
{
    public function handle($request, $next)
    {
        // Verify if the authed user is not the kiosk user if so redirect to kiosk page
        if (Auth::id() == 1) {
            return redirect('kiosk');
        }

        return $next($request);
    }
}
