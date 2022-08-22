<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class VerifyLeaderboards
{
    public function handle($request, $next)
    {
        // Verify if the leaderboards page is enabled
        if (Setting::get('leaderboards_enabled') == 'true') {
            return $next($request);
        }

        // Else verify if the authed user is at least a manager
        if (Auth::user()->manager) {
            return $next($request);
        }

        abort(403);
    }
}
