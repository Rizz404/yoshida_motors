<?php

use App\Http\Controllers\Admin\AppraisalRequestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Root: redirect to default locale
Route::get('/', fn () => redirect('/en'));

// Locale-prefixed routes (/{locale}/...)
// SetLocale middleware reads {locale}, calls App::setLocale() and URL::defaults()
Route::prefix('{locale}')
    ->where(['locale' => 'en|ja'])
    ->middleware(\App\Http\Middleware\SetLocale::class)
    ->group(function () {

        // Redirect bare locale root to login
        Route::get('/', fn () => redirect()->route('login'));

        // Guest routes
        Route::middleware('guest')->group(function () {
            Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
            Route::post('/login', [LoginController::class, 'login'])->name('login.post');
        });

        // Authenticated (admin) routes
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

            Route::get('/dashboard', DashboardController::class)->name('dashboard');
        });
    });
