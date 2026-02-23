<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppraisalRequestController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Public routes

    // Phone OTP — combined login/register (dipakai oleh register_screen.dart)
    Route::post('/auth/phone', [AuthController::class, 'loginOrRegisterWithPhone'])
        ->name('auth.phone');

    // Phone OTP (legacy separate endpoints, tetap dipertahankan)
    Route::post('/auth/register', [AuthController::class, 'registerWithFirebase'])
        ->name('auth.register');
    Route::post('/auth/login', [AuthController::class, 'loginWithFirebase'])
        ->name('auth.login');

    // Email & Password
    Route::post('/auth/register/email', [AuthController::class, 'registerWithEmailPassword'])
        ->name('auth.register.email');
    Route::post('/auth/login/email', [AuthController::class, 'loginWithEmailPassword'])
        ->name('auth.login.email');

    // Google Sign-In (auto register + login)
    Route::post('/auth/login/google', [AuthController::class, 'loginWithGoogle'])
        ->name('auth.login.google');

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth endpoints
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
            Route::put('/profile', [AuthController::class, 'updateProfile'])->name('update-profile');
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        });

        // Appraisal endpoints
        Route::prefix('appraisals')->name('appraisals.')->group(function () {
            Route::get('/latest', [AppraisalRequestController::class, 'latest'])->name('latest');
            Route::get('/', [AppraisalRequestController::class, 'index'])->name('index');
            Route::post('/', [AppraisalRequestController::class, 'store'])->name('store');
            Route::get('/{id}', [AppraisalRequestController::class, 'show'])->name('show')->whereNumber('id');
            Route::put('/{id}', [AppraisalRequestController::class, 'update'])->name('update')->whereNumber('id');
            Route::delete('/{id}', [AppraisalRequestController::class, 'destroy'])->name('destroy')->whereNumber('id');

            // Submit appraisal
            Route::post('/{id}/submit', [AppraisalRequestController::class, 'submit'])->name('submit')->whereNumber('id');
        });

        // Notification endpoints
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::put('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read')->whereNumber('id');
        });
    });
});
