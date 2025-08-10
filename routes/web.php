<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [App\Http\Controllers\Customer\HomeController::class, 'index'])->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'check.user.active'])->name('dashboard');

// Admin routes - Only Admin & Staff can access
Route::middleware(['auth', 'verified', 'check.user.active'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::get('users/{user}/details', [App\Http\Controllers\Admin\UserController::class, 'getUserDetails'])->name('users.details');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    
    // Add other admin routes here
    // Route::resource('orders', App\Http\Controllers\Admin\OrderController::class);
    // Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    // Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
});

// API routes for validation (no CSRF for AJAX calls)
Route::prefix('api/validation')->middleware('web')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::post('/check-email', [App\Http\Controllers\Api\ValidationController::class, 'checkEmail'])->name('api.validation.check-email');
    Route::post('/validate-password', [App\Http\Controllers\Api\ValidationController::class, 'validatePassword'])->name('api.validation.validate-password');
    Route::post('/check-login', [App\Http\Controllers\Api\ValidationController::class, 'checkLogin'])->name('api.validation.check-login');
    
    // Test route
    Route::get('/test', function() {
        return response()->json(['message' => 'API is working', 'method' => 'GET']);
    });
    Route::post('/test', function() {
        return response()->json(['message' => 'API POST is working', 'method' => 'POST']);
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
