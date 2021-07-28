<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PagesController::class, 'home'])->name('home');

// Normal routes
Route::middleware('auth')->group(function () {
    Route::view('/auth/settings', 'settings')->name('settings');

    Route::get('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::view('/auth/login', 'auth.login')->name('auth.login');
});
