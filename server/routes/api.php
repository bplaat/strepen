<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Support\Facades\Route;

Route::get('', [ApiController::class, 'home'])->name('api.home');

// Api auth routes
Route::middleware('api_key')->group(function () {
    // TODO

    Route::get('auth/logout', [ApiAuthController::class, 'logout'])->name('api.auth.logout');
});

// Api guest routes
Route::middleware('api_key:false')->group(function () {
    Route::any('auth/login', [ApiAuthController::class, 'login'])->name('api.auth.login');
});
