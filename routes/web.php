<?php

use App\Http\Controllers\Admin\AppraisalRequestController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Halaman Depan (Redirect ke login aja biar simpel)
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest (Yang belum login boleh masuk sini)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Admin (Yang udah login & role admin)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('users', UserController::class);
    Route::resource('appraisals', AppraisalRequestController::class);

    Route::get('/dashboard', function () {
        return view('admin.dashboard'); // Nanti kita buat file ini
    })->name('dashboard');
});
