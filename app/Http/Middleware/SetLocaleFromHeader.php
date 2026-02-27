<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Supported locales for the application.
     */
    protected array $supportedLocales = ['en', 'ja'];

    /**
     * Handle an incoming request.
     *
     * Reads the Accept-Language header and sets the best matching locale.
     * Example header value: "ja-JP,ja;q=0.9,en-US;q=0.8,en;q=0.7"
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request->header('Accept-Language', 'en'));

        App::setLocale($locale);

        return $next($request);
    }

    /**
     * Parse the Accept-Language header and return the best supported locale.
     */
    private function resolveLocale(string $header): string
    {
        $candidates = [];

        foreach (explode(',', $header) as $part) {
            $part  = trim($part);
            $split = explode(';q=', $part);

            $tag     = strtolower(trim($split[0]));  // e.g. "ja-JP" or "en"
            $quality = isset($split[1]) ? (float) $split[1] : 1.0;
            $short   = explode('-', $tag)[0];         // "ja-JP" -> "ja"

            // Store highest quality for each short code (avoid duplicates)
            if (! isset($candidates[$short]) || $candidates[$short] < $quality) {
                $candidates[$short] = $quality;
            }
        }

        // Sort by quality descending
        arsort($candidates);

        foreach (array_keys($candidates) as $lang) {
            if (in_array($lang, $this->supportedLocales)) {
                return $lang;
            }
        }

        return 'en';
    }
}
