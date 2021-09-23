<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiUsersController;
use App\Http\Controllers\Api\ApiNotificationsController;
use App\Http\Controllers\Api\ApiPostsController;
use App\Http\Controllers\Api\ApiProductsController;
use App\Http\Controllers\Api\ApiInventoriesController;
use App\Http\Controllers\Api\ApiTransactionsController;
use Illuminate\Support\Facades\Route;

Route::get('', [ApiController::class, 'home'])->name('api.home');

// Api admin routes
Route::middleware('api_key:admin')->group(function () {
    Route::get('users/check_balances', [ApiUsersController::class, 'checkBalances'])->name('api.users.check_balances');
    Route::get('users/{user}/inventories', [ApiUsersController::class, 'showInventories'])->name('api.users.show_inventories');

    Route::get('inventories', [ApiInventoriesController::class, 'index'])->name('api.inventories.index');
    Route::get('inventories/{inventory}', [ApiInventoriesController::class, 'show'])->name('api.inventories.show');

    Route::get('transactions', [ApiTransactionsController::class, 'index'])->name('api.transactions.index');

    // TODO
});

// Api self routes
Route::middleware('api_key:self')->group(function () {
    Route::get('users/{user}/transactions', [ApiUsersController::class, 'showTransactions'])->name('api.users.show_transactions');
    Route::get('users/{user}/notifications', [ApiUsersController::class, 'showNotifications'])->name('api.users.show_notifications');
    Route::get('users/{user}/notifications/unread', [ApiUsersController::class, 'showUnreadNotifications'])->name('api.users.show_unread_notifications');

    Route::get('notifications/{notification}/read', [ApiNotificationsController::class, 'read'])->name('api.notifications.read');

    Route::get('transactions/{transaction}', [ApiTransactionsController::class, 'show'])->name('api.transactions.show');

    // TODO
});

// Api auth routes
Route::middleware('api_key:auth')->group(function () {
    Route::get('users', [ApiUsersController::class, 'index'])->name('api.users.index');
    Route::get('users/{user}', [ApiUsersController::class, 'show'])->name('api.users.show');

    Route::get('products', [ApiProductsController::class, 'index'])->name('api.products.index');
    Route::get('products/{product}', [ApiProductsController::class, 'show'])->name('api.products.show');

    Route::post('transactions', [ApiTransactionsController::class, 'store'])->name('api.transactions.store');

    Route::get('auth/logout', [ApiAuthController::class, 'logout'])->name('api.auth.logout');
});

// Api guest routes
Route::middleware('api_key:guest')->group(function () {
    Route::get('users/{user}/posts', [ApiUsersController::class, 'showPosts'])->name('api.users.show_posts');

    Route::get('posts', [ApiPostsController::class, 'index'])->name('api.posts.index');
    Route::get('posts/{post}', [ApiPostsController::class, 'show'])->name('api.posts.show');

    Route::any('auth/login', [ApiAuthController::class, 'login'])->name('api.auth.login');
});
