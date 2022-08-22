<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class VerifyCasino
{
    public function handle($request, $next)
    {
        // Verify if the casino page is enabled
        if (Setting::get('casino_enabled') == 'true') {
            return $next($request);
        }

        // Else verify if the authed user is an admin
        if (Auth::user()->admin) {
            return $next($request);
        }

        abort(403);
    }
}
