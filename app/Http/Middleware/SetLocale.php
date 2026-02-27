<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales for the application.
     */
    protected array $supportedLocales = ['en', 'ja'];

    /**
     * Handle an incoming request.
     *
     * Reads the {locale} segment from the URL route parameter,
     * sets the application locale, and registers it as a URL default
     * so all route() helper calls automatically include the locale.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale', 'en');

        if (! in_array($locale, $this->supportedLocales)) {
            abort(404);
        }

        App::setLocale($locale);

        // Make route() helpers always inject {locale} without explicit passing.
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
