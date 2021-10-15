<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class VerifyCanKiosk
{
    public function handle($request, $next)
    {
        // Verify if ip is in kiosk ip whitelist
        if (in_array(Request::ip(), array_map('trim', explode(',', Setting::get('kiosk_ip_whitelist'))))) {
            return $next($request);
        }

        // Or verify if the authed user is an admin
        if (Auth::check() && Auth::user()->role == User::ROLE_ADMIN) {
            return $next($request);
        }

        abort(403);
    }
}
