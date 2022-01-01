<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifyCanKiosk
{
    public function handle($request, $next)
    {
        // Verify if ip is in kiosk ip whitelist
        if (in_array($request->ip(), array_map('trim', explode(',', Setting::get('kiosk_ip_whitelist'))))) {
            return $next($request);
        }

        // Or verify if the authed user is a manager or an admin
        if (Auth::check() && Auth::user()->manager) {
            return $next($request);
        }

        abort(403);
    }
}
