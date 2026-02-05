<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppraisalRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Public routes
    Route::post('/auth/firebase', [AuthController::class, 'loginWithFirebase'])
        ->name('auth.firebase');

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
            Route::get('/', [AppraisalRequestController::class, 'index'])->name('index');
            Route::post('/', [AppraisalRequestController::class, 'store'])->name('store');
            Route::get('/{id}', [AppraisalRequestController::class, 'show'])->name('show');
            Route::put('/{id}', [AppraisalRequestController::class, 'update'])->name('update');
            Route::delete('/{id}', [AppraisalRequestController::class, 'destroy'])->name('destroy');

            // Photo management
            Route::post('/{id}/photos', [AppraisalRequestController::class, 'uploadPhoto'])->name('upload-photo');
            Route::delete('/{appraisalId}/photos/{photoId}', [AppraisalRequestController::class, 'deletePhoto'])->name('delete-photo');

            // Submit appraisal
            Route::post('/{id}/submit', [AppraisalRequestController::class, 'submit'])->name('submit');
        });
    });
});
