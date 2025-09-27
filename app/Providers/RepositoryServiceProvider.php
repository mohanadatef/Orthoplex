<?php
namespace App\Providers;
use App\Repositories\{Eloquent\AnalyticsRepository,
    Eloquent\ApiKeyRepository,
    Eloquent\AuthRepository,
    Eloquent\DeleteRequestRepository};
use App\Repositories\Contracts\{AnalyticsRepositoryInterface,
    ApiKeyRepositoryInterface,
    AuthRepositoryInterface,
    DeleteRequestRepositoryInterface};
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider {
    public function register() {
        $this->app->bind(DeleteRequestRepositoryInterface::class, DeleteRequestRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(ApiKeyRepositoryInterface::class, ApiKeyRepository::class);
        $this->app->bind(AnalyticsRepositoryInterface::class, AnalyticsRepository::class);
    }
}
