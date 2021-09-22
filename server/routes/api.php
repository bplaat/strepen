<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiUsersController;
use App\Http\Controllers\Api\ApiPostsController;
use Illuminate\Support\Facades\Route;

Route::get('', [ApiController::class, 'home'])->name('api.home');

// Api auth routes
Route::middleware('api_key')->group(function () {
    // TODO

    Route::get('users', [ApiUsersController::class, 'index'])->name('api.users.index');
    Route::get('users/{user}', [ApiUsersController::class, 'show'])->name('api.users.show');

    Route::get('posts', [ApiPostsController::class, 'index'])->name('api.posts.index');
    Route::get('posts/{post}', [ApiPostsController::class, 'show'])->name('api.posts.show');

    Route::get('auth/logout', [ApiAuthController::class, 'logout'])->name('api.auth.logout');
});

// Api guest routes
Route::middleware('api_key:false')->group(function () {
    Route::any('auth/login', [ApiAuthController::class, 'login'])->name('api.auth.login');
});
