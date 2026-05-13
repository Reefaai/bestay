<?php

use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminPaymentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public authentication routes with rate limiting (5 requests per minute)
Route::middleware('throttle:auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Public room routes (listing and details)
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{room}', [RoomController::class, 'show']);

// Protected routes (require valid Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // Room management (admin only, enforced by policy)
    Route::post('/rooms', [RoomController::class, 'store']);
    Route::put('/rooms/{room}', [RoomController::class, 'update']);
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);

    // Room availability check
    Route::get('/rooms/{room}/availability', [RoomController::class, 'availability']);

    // Booking routes (user)
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Payment routes (guest)
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::post('/payments/{payment}/method', [PaymentController::class, 'selectMethod']);
    Route::post('/payments/{payment}/process', [PaymentController::class, 'process']);
    Route::post('/payments/{payment}/retry', [PaymentController::class, 'retry']);

    // Admin routes (require admin role)
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/bookings', [AdminBookingController::class, 'index']);
        Route::get('/bookings/conflicts', [AdminBookingController::class, 'conflicts']);
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show']);
        Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus']);

        Route::get('/payments', [AdminPaymentController::class, 'index']);
        Route::get('/payments/{payment}', [AdminPaymentController::class, 'show']);
        Route::patch('/payments/{payment}/status', [AdminPaymentController::class, 'updateStatus']);
    });
});
