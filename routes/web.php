<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [App\Http\Controllers\Customer\HomeController::class, 'index'])->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes - Only Admin & Staff can access
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Add other admin routes here
    // Route::resource('orders', App\Http\Controllers\Admin\OrderController::class);
    // Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    // Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    // Route::resource('users', App\Http\Controllers\Admin\UserController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
