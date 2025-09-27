<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SetLocaleFromHeader
{
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->header('Accept-Language') ?: 'en';
        $lang = Str::startsWith($lang,'ar') ? 'ar' : 'en';
        app()->setLocale($lang);
        return $next($request);
    }
}
