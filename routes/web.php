<?php

use App\Http\Controllers\Web\AdminBookingController;
use App\Http\Controllers\Web\AdminDashboardController;
use App\Http\Controllers\Web\AdminPaymentController;
use App\Http\Controllers\Web\AdminRoomController;
use App\Http\Controllers\Web\AdminUserController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Room routes (public)
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

// Booking routes (authenticated)
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store')->middleware('auth');

// Dashboard routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings/{booking}', [DashboardController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [DashboardController::class, 'cancelBooking'])->name('bookings.cancel');
});

// Payment routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/bookings/{booking}/payment', [PaymentController::class, 'show'])->name('bookings.payment');
    Route::post('/payments/{payment}/method', [PaymentController::class, 'selectMethod'])->name('payments.method');
    Route::get('/payments/{payment}/confirm', [PaymentController::class, 'confirmForm'])->name('payments.confirm');
    Route::post('/payments/{payment}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm.submit');
    Route::post('/payments/{payment}/retry', [PaymentController::class, 'retry'])->name('payments.retry');
});

// Admin routes (authenticated + admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Room management
    Route::get('/rooms', [AdminRoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/create', [AdminRoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [AdminRoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}/edit', [AdminRoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [AdminRoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [AdminRoomController::class, 'destroy'])->name('rooms.destroy');

    // Booking management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/conflicts', [AdminBookingController::class, 'conflicts'])->name('bookings.conflicts');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.updateStatus');

    // Payment monitoring & verification
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::patch('/payments/{payment}/status', [AdminPaymentController::class, 'updateStatus'])->name('payments.updateStatus');

    // User management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
});
