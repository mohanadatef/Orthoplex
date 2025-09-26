<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Localization {
    public function handle(Request $request, Closure $next) {
        $locale = $request->header('Accept-Language')
            ?? $request->query('lang')
            ?? auth()->check() ? auth()->user()->locale : config('app.locale');
        if (!in_array($locale, ['en','ar'])) $locale = config('app.locale');
        App::setLocale($locale);
        return $next($request);
    }
}
