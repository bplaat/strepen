<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiInventoriesController;
use App\Http\Controllers\Api\ApiNotificationsController;
use App\Http\Controllers\Api\ApiPostsController;
use App\Http\Controllers\Api\ApiProductsController;
use App\Http\Controllers\Api\ApiSettingsController;
use App\Http\Controllers\Api\ApiTransactionsController;
use App\Http\Controllers\Api\ApiUsersController;
use Illuminate\Support\Facades\Route;

Route::get('', function () {
    return [
        'message' => 'Strepen REST API documentation: ' . asset('/api.html'),
    ];
})->name('api.home');

// Api guest routes
Route::middleware('api_key:guest')->group(function () {
    Route::post('auth/login', [ApiAuthController::class, 'login'])->name('api.auth.login');
});

// Api auth routes
Route::middleware('api_key:auth')->group(function () {
    Route::get('auth/validate', [ApiAuthController::class, 'validate'])->name('api.auth.validate');

    Route::get('users', [ApiUsersController::class, 'index'])->name('api.users.index');
    Route::get('users/{user}', [ApiUsersController::class, 'show'])->name('api.users.show');
    Route::get('users/{user}/posts', [ApiUsersController::class, 'userPosts'])->name('api.users.user_posts');

    Route::get('posts', [ApiPostsController::class, 'index'])->name('api.posts.index');
    Route::get('posts/{post}', [ApiPostsController::class, 'show'])->name('api.posts.show');
    Route::get('posts/{post}/like', [ApiPostsController::class, 'like'])->name('api.posts.like');
    Route::get('posts/{post}/dislike', [ApiPostsController::class, 'dislike'])->name('api.posts.dislike');

    Route::get('products', [ApiProductsController::class, 'index'])->name('api.products.index');
    Route::get('products/active', [ApiProductsController::class, 'indexActive'])->name('api.products.index_active');
    Route::get('products/{product}', [ApiProductsController::class, 'show'])->name('api.products.show');

    Route::post('transactions', [ApiTransactionsController::class, 'store'])->name('api.transactions.store');

    Route::get('settings', [ApiSettingsController::class, 'index'])->name('api.settings.index');

    Route::get('auth/logout', [ApiAuthController::class, 'logout'])->name('api.auth.logout');
});

// Api self routes
Route::middleware('api_key:self')->group(function () {
    Route::get('users/{user}/transactions', [ApiUsersController::class, 'userTransactions'])->name('api.users.user_transactions');
    Route::get('users/{user}/notifications', [ApiUsersController::class, 'userNotifications'])->name('api.users.user_notifications');
    Route::get('users/{user}/notifications/unread', [ApiUsersController::class, 'userNotificationsUnread'])->name('api.users.user_notifications_unread');
    Route::post('users/{user}/edit', [ApiUsersController::class, 'edit'])->name('api.users.edit');

    Route::get('notifications/{notification}/read', [ApiNotificationsController::class, 'read'])->name('api.notifications.read');

    Route::get('transactions/{transaction}', [ApiTransactionsController::class, 'show'])->name('api.transactions.show');
});

// Api manager routes
Route::middleware('api_key:manager')->group(function () {
    Route::get('users/check_balances', [ApiUsersController::class, 'checkBalances'])->name('api.users.check_balances');
    Route::get('users/{user}/inventories', [ApiUsersController::class, 'userInventories'])->name('api.users.user_inventories');

    Route::get('inventories', [ApiInventoriesController::class, 'index'])->name('api.inventories.index');
    Route::get('inventories/{inventory}', [ApiInventoriesController::class, 'show'])->name('api.inventories.show');

    Route::get('transactions', [ApiTransactionsController::class, 'index'])->name('api.transactions.index');
});
