<?php

use App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Support\Facades\Route;

// Api home route
Route::get('', function () {
    return [
        'message' => 'Strepen REST API documentation: https://github.com/bplaat/strepen/blob/master/docs/api.md'
    ];
})->name('api.home');

// Api auth routes
Route::middleware('api_key')->group(function () {



    Route::get('auth/logout', [ApiAuthController::class, 'logout'])->name('api.auth.logout');
});

// Api guest routes
Route::middleware('api_key:false')->group(function () {
    Route::any('auth/login', [ApiAuthController::class, 'login'])->name('api.auth.login');
});
