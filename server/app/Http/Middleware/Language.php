<?php

namespace App\Http\Middleware;

use App;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Language
{
    public function handle($request, $next)
    {
        // Select english language when user selected
        if (Auth::check() && Auth::user()->language == User::LANGUAGE_ENGLISH) {
            App::setLocale('en');
        }

        return $next($request);
    }
}
