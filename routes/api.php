<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;


/**
 * AUTHENTICATION 
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/forgot-password/reset', [AuthController::class, 'resetPasswordByOtp']);
Route::post('/convert-guest', [AuthController::class, 'convertGuestToMember']);

Route::middleware('web')->group(function () {
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});

/**
 * PRODUCTS
 */
// Public product endpoints
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/list', function() {
    return response()->json(\App\Models\Product::select('id', 'name')->get());
});
Route::get('/categories', function () {
    return response()->json(\App\Models\Category::all());
});

Route::post('/products/{product}/reviews', [App\Http\Controllers\ReviewController::class, 'store']);

// CRUD product routes
Route::post('/products', [ProductController::class, 'store']);
Route::patch('/products/{product}/update-price', [ProductController::class, 'updatePrice']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::get('/admin/products-list', function() {
        return response()->json(\App\Models\Product::select('id', 'name')->get());
    });
});

/**
 * BLOGS
 */
// Public blog endpoints
Route::get('/blogs', [BlogController::class, 'index']);
Route::post('/blogs', [BlogController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/blogs/{blog}', [BlogController::class, 'update']);
    Route::delete('/blogs/{blog}', [BlogController::class, 'destroy']);
});

/**
 * ORDERS
 */
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/user/orders', [OrderController::class, 'userOrders']);
    Route::get('/admin/orders', [OrderController::class, 'index']);
    Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus']);
});

/**
 * DASHBOARD & ADMIN
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/stats', [DashboardController::class, 'index']);
    Route::get('/admin/sales-chart', [DashboardController::class, 'getSalesChart']);
});


use App\Http\Controllers\UserController;

Route::get('/admin/users/search', [UserController::class, 'searchByEmail']);
Route::get('/admin/users/admins', [UserController::class, 'indexAdmins']);
Route::patch('/admin/users/{user}/toggle-admin', [UserController::class, 'toggleAdmin']);



Route::get('/products/{product:slug}', [ProductController::class, 'show']);
Route::get('/top-rated-products', [\App\Http\Controllers\ProductController::class, 'getTopRated']);
