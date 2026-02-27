<?php

use App\Http\Controllers\Admin\AppraisalRequestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Halaman Depan (Redirect ke login aja biar simpel)
Route::get('/', function () {
    return redirect()->route('login');
});

// Locale Switcher
Route::get('/locale/{locale}', function (string $locale) {
    $supported = ['en', 'ja'];
    if (in_array($locale, $supported)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');

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

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // * Karena pake invoke jadi gak usah pake array padahal bukan resource
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});
