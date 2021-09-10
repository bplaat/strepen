<?php

namespace App\Http\Middleware;

use App;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Language
{
    public function handle($request, $next)
    {
        // Select dutch language when user selected
        if (Auth::check() && Auth::user()->language == User::LANGUAGE_DUTCH) {
            App::setLocale('nl');
        }

        return $next($request);
    }
}
