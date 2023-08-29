<?php

use Illuminate\Support\Facades\Route;

Route::get('/', App\Http\Livewire\Home::class)->name('home');

Route::view('/apps', 'apps')->name('apps');

Route::view('/release-notes', 'release-notes')->name('release-notes');

Route::view('/account-deletion', 'account-deletion')->name('account-deletion');

// Auth routes
Route::middleware('auth')->group(function () {
    Route::get('/posts/{post}', App\Http\Livewire\Posts\Show::class)->name('posts.show');

    Route::get('/stripe', App\Http\Livewire\Transactions\Create::class)->name('transactions.create');

    Route::middleware('leaderboards')->group(function () {
        Route::get('/leaderboards', App\Http\Livewire\Leaderboards::class)->name('leaderboards');
    });

    Route::middleware('casino')->group(function () {
        Route::get('/casino', App\Http\Livewire\Casino::class)->name('casino');
    });

    Route::get('/auth/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('auth.logout');

    // No kiosk routes
    Route::middleware('nokiosk')->group(function () {
        Route::get('/transactions', App\Http\Livewire\Transactions\History::class)->name('transactions.history');

        Route::get('/notifications', App\Http\Livewire\Notifications::class)->name('notifications');

        Route::get('/balance', App\Http\Livewire\Balance::class)->name('balance');

        Route::view('/settings', 'settings')->name('settings');
    });

    // Manager routes
    Route::middleware('manager')->group(function () {
        Route::view('/admin', 'admin.home')->name('admin.home');

        Route::get('/admin/users', App\Http\Livewire\Admin\Users\Crud::class)->name('admin.users.crud');

        Route::get('/admin/posts', App\Http\Livewire\Admin\Posts\Crud::class)->name('admin.posts.crud');

        Route::get('/admin/products', App\Http\Livewire\Admin\Products\Crud::class)->name('admin.products.crud');

        Route::get('/admin/inventories', App\Http\Livewire\Admin\Inventories\Crud::class)->name('admin.inventories.crud');

        Route::get('/admin/transactions', App\Http\Livewire\Admin\Transactions\Crud::class)->name('admin.transactions.crud');
    });

    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::view('/admin/settings', 'admin.settings')->name('admin.settings');

        Route::get('/admin/api_keys', App\Http\Livewire\Admin\ApiKeys\Crud::class)->name('admin.api_keys.crud');
    });
});

// Can kiosk routes
Route::middleware('cankiosk')->group(function () {
    Route::get('/admin/kiosk', [App\Http\Controllers\Admin\AdminController::class, 'kiosk'])->name('admin.kiosk');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/auth/login', App\Http\Livewire\Auth\Login::class)->name('auth.login');

    Route::get('/auth/forgot-password', App\Http\Livewire\Auth\ForgotPassword::class)->name('auth.forgot_password');

    Route::get('/auth/reset-password/{token}', App\Http\Livewire\Auth\ResetPassword::class)->name('password.reset');
});
