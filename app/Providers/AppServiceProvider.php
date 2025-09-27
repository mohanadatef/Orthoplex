<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Domain\Repositories\UserRepository;
use App\Repositories\EloquentUserRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
    }

    public function boot(): void {}
}
