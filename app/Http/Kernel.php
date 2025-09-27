<?php
namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middlewareAliases = [
        'setlocale' => \App\Http\Middleware\SetLocaleFromHeader::class,
        'ratemetrics' => \App\Http\Middleware\RateMetrics::class,
        'orgapikey' => \App\Http\Middleware\OrgApiKeyAuth::class,
        'audit' => \App\Http\Middleware\AuditActions::class,
        'idempotent' => \App\Http\Middleware\EnsureIdempotency::class,
    ];
}
