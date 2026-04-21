<?php

use App\Http\Controllers\Admin\AppraisalRequestController;
use App\Http\Controllers\Admin\AuctionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Dealer\BidController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use \App\Http\Middleware\SetLocale;

// Root: redirect to default locale
Route::get('/', fn() => redirect('/en'));

// Locale-prefixed routes (/{locale}/...)
// SetLocale middleware reads {locale}, calls App::setLocale() and URL::defaults()
Route::prefix('{locale}')
    ->where(['locale' => 'en|ja'])
    ->middleware(SetLocale::class)
    ->group(function () {

        // Redirect bare locale root to login
        Route::get('/', fn() => redirect()->route('login'));

        // Guest routes
        Route::middleware('guest')->group(function () {
            Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
            Route::post('/login', [LoginController::class, 'login'])->name('login.post');
        });

        // Authenticated routes
        Route::middleware('auth')->group(function () {
            Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

            // Admin only routes
            Route::middleware('role:admin')->group(function () {
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

                // Auctions
                Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');
                Route::get('/auctions/create', [AuctionController::class, 'create'])->name('auctions.create');
                Route::post('/auctions', [AuctionController::class, 'store'])->name('auctions.store');
                Route::get('/auctions/{auction}', [AuctionController::class, 'show'])->name('auctions.show');
                Route::post('/auctions/{auction}/award', [AuctionController::class, 'award'])->name('auctions.award');
            });

            // Dealer only routes
            Route::middleware('role:dealer')->prefix('dealer')->name('dealer.')->group(function () {
                Route::get('/marketplace', [App\Http\Controllers\Dealer\MarketplaceController::class, 'index'])->name('marketplace.index');
                Route::get('/marketplace/{id}', [App\Http\Controllers\Dealer\MarketplaceController::class, 'show'])->name('marketplace.show');
                Route::post('/marketplace/{id}/bid', [BidController::class, 'store'])->name('bids.store');
                Route::get('/bids', [BidController::class, 'index'])->name('bids.index');
            });
        });
    });
