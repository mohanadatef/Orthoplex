<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // الأولوية: query string > header > default
        $locale = $request->query('lang')
            ?? $request->header('Accept-Language')
            ?? config('app.locale');

        if (! in_array($locale, ['en', 'ar'])) {
            $locale = config('app.locale'); // fallback
        }

        App::setLocale($locale);

        return $next($request);
    }
}
