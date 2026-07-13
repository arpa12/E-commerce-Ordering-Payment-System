<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/payments/stripe/webhook', [PaymentController::class, 'webhookStripe']);
Route::match(['get', 'post'], '/payments/bkash/callback', [PaymentController::class, 'executeBkash'])->name('bkash.callback');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/user/toggle-admin', function (Request $request) {
        $user = $request->user();
        $user->is_admin = !$user->is_admin;
        $user->save();
        return response()->json(['message' => 'Admin status updated successfully.', 'user' => $user]);
    });

    Route::get('/orders', [UserController::class, 'orders']);
    Route::get('/payments', [UserController::class, 'payments']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Payment routes
    Route::post('/payments/checkout', [PaymentController::class, 'checkout']);
    Route::post('/payments/stripe/confirm', [PaymentController::class, 'confirmStripe']);
    Route::post('/payments/bkash/execute', [PaymentController::class, 'executeBkash']);
    Route::get('/payments/bkash/query/{transaction_id}', [PaymentController::class, 'queryBkash']);

    // Admin-only routes
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });
});

