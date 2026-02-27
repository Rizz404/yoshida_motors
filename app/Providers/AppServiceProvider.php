<?php

namespace App\Providers;

use App\Models\AppraisalRequest;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Explicit route model bindings to avoid ambiguity with the
        // "AppraisalRequest" name (which Laravel may confuse with a FormRequest).
        Route::model('appraisal', AppraisalRequest::class);
        Route::model('user', User::class);
    }
}
