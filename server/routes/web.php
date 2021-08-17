<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\Admin\AdminInventoriesController;
use App\Http\Controllers\Admin\AdminPostsController;
use App\Http\Controllers\Admin\AdminProductsController;
use App\Http\Controllers\Admin\AdminTransactionsController;
use App\Http\Controllers\Admin\AdminUsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PagesController::class, 'home'])->name('home');

// Normal routes
Route::middleware('auth')->group(function () {
    Route::view('/auth/settings', 'settings')->name('settings');

    Route::get('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

// Admin routes
Route::middleware('admin')->group(function () {
    Route::view('/admin', 'admin.home')->name('admin.home');

    Route::get('/admin/users', [AdminUsersController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{user}', [AdminUsersController::class, 'show'])->name('admin.users.show');

    Route::get('/admin/posts', [AdminPostsController::class, 'index'])->name('admin.posts.index');
    Route::get('/admin/posts/{post}', [AdminPostsController::class, 'show'])->name('admin.posts.show');

    Route::get('/admin/products', [AdminProductsController::class, 'index'])->name('admin.products.index');
    Route::get('/admin/products/{product}', [AdminProductsController::class, 'show'])->name('admin.products.show');

    Route::get('/admin/inventories', [AdminInventoriesController::class, 'index'])->name('admin.inventories.index');
    Route::get('/admin/inventories/{inventory}', [AdminInventoriesController::class, 'show'])->name('admin.inventories.show');

    Route::get('/admin/transactions', [AdminTransactionsController::class, 'index'])->name('admin.transactions.index');
    Route::get('/admin/transactions/{transaction}', [AdminTransactionsController::class, 'show'])->name('admin.transactions.show');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::view('/auth/login', 'auth.login')->name('auth.login');
});
