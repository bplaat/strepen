<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', App\Http\Livewire\Home::class)->name('home');

// Normal routes
Route::middleware('auth')->group(function () {
    Route::get('stripe', App\Http\Livewire\Transactions\Create::class)->name('transactions.create');
    Route::get('transactions', App\Http\Livewire\Transactions\History::class)->name('transactions.history');

    Route::view('/auth/settings', 'settings')->name('settings');

    Route::get('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

// Admin routes
Route::middleware('admin')->group(function () {
    Route::view('/admin', 'admin.home')->name('admin.home');

    Route::get('/admin/users', App\Http\Livewire\Admin\Users\Crud::class)->name('admin.users.index');

    Route::get('/admin/posts', App\Http\Livewire\Admin\Posts\Crud::class)->name('admin.posts.index');

    Route::get('/admin/products', App\Http\Livewire\Admin\Products\Crud::class)->name('admin.products.index');

    Route::get('/admin/inventories', App\Http\Livewire\Admin\Inventories\Crud::class)->name('admin.inventories.index');

    Route::get('/admin/transactions', App\Http\Livewire\Admin\Transactions\Crud::class)->name('admin.transactions.index');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/auth/login', App\Http\Livewire\Auth\Login::class)->name('auth.login');
});
