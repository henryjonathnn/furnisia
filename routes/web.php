<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [App\Http\Controllers\Customer\HomeController::class, 'index'])->name('home');

// Customer Product routes
Route::get('/products', [App\Http\Controllers\Customer\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [App\Http\Controllers\Customer\ProductController::class, 'show'])->name('products.show');

// Customer Cart routes
Route::middleware(['auth', 'verified', 'check.user.active'])->group(function () {
    Route::get('/cart', [App\Http\Controllers\Customer\CartController::class, 'index'])->name('cart.index');
});

// Customer Order routes
Route::middleware(['auth', 'verified', 'check.user.active'])->group(function () {
    Route::get('/my-orders', [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders.index');
    Route::get('/my-orders/{id}/payment', [App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.payment');
    Route::get('/my-orders/{id}/invoice', [App\Http\Controllers\Customer\OrderController::class, 'downloadInvoice'])->name('orders.invoice');
});

// Search suggestions API
Route::get('/api/search/suggestions', [App\Http\Controllers\Customer\ProductController::class, 'suggestions'])->name('search.suggestions');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'check.user.active'])->name('dashboard');

// Admin routes - Only Admin & Staff can access
Route::middleware(['auth', 'verified', 'check.user.active'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::get('users/{user}/details', [App\Http\Controllers\Admin\UserController::class, 'getUserDetails'])->name('users.details');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    
    // Product Management
    Route::get('products/{product}/details', [App\Http\Controllers\Admin\ProductController::class, 'details'])->name('products.details');
    Route::post('products/{product}/update-stock', [App\Http\Controllers\Admin\ProductController::class, 'updateStock'])->name('products.update-stock');
    Route::post('products/{id}/restore', [App\Http\Controllers\Admin\ProductController::class, 'restore'])->name('products.restore');
    Route::delete('products/{id}/force-delete', [App\Http\Controllers\Admin\ProductController::class, 'forceDelete'])->name('products.force-delete');
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    
    // Order Management
    Route::get('orders/{order}/details', [App\Http\Controllers\Admin\OrderController::class, 'details'])->name('orders.details');
    Route::put('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/complete', [App\Http\Controllers\Admin\OrderController::class, 'completeOrder'])->name('orders.complete');
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show']);
    
    // Wallet Management
    Route::get('wallet/balance', [App\Http\Controllers\Admin\WalletController::class, 'getBalance'])->name('wallet.balance');
    
    // Add other admin routes here
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

// API routes for cart operations
Route::prefix('api/cart')->middleware('web')->group(function () {
    Route::get('/count', [App\Http\Controllers\Customer\CartController::class, 'getCartCount'])->name('api.cart.count');
    
    Route::middleware(['auth', 'verified', 'check.user.active'])->group(function () {
        Route::post('/', [App\Http\Controllers\Customer\CartController::class, 'store'])->name('api.cart.store');
        Route::put('/{id}', [App\Http\Controllers\Customer\CartController::class, 'update'])->name('api.cart.update');
        Route::post('/{id}/toggle-selection', [App\Http\Controllers\Customer\CartController::class, 'toggleSelection'])->name('api.cart.toggle-selection');
        Route::post('/toggle-select-all', [App\Http\Controllers\Customer\CartController::class, 'toggleSelectAll'])->name('api.cart.toggle-select-all');
        Route::post('/checkout', [App\Http\Controllers\Customer\CartController::class, 'checkout'])->name('api.cart.checkout');
        Route::delete('/{id}', [App\Http\Controllers\Customer\CartController::class, 'destroy'])->name('api.cart.destroy');
        Route::delete('/remove-selected', [App\Http\Controllers\Customer\CartController::class, 'destroySelected'])->name('api.cart.destroy-selected');
    });
});

// API routes for order operations
Route::prefix('api/orders')->middleware('web')->group(function () {
    Route::middleware(['auth', 'verified', 'check.user.active'])->group(function () {
        Route::post('/buy-now', [App\Http\Controllers\Customer\OrderController::class, 'buyNow'])->name('api.orders.buy-now');
        Route::post('/{id}/payment', [App\Http\Controllers\Customer\OrderController::class, 'processPayment'])->name('api.orders.payment');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
