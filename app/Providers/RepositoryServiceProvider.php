<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\{OrgRepositoryInterface,WebhookRepositoryInterface,ApiKeyRepositoryInterface};
use App\Repositories\{OrgRepository,WebhookRepository,ApiKeyRepository};
use App\Services\Contracts\{OrgServiceInterface,WebhookServiceInterface,ApiKeyServiceInterface};
use App\Services\{OrgService,WebhookService,ApiKeyService};

class RepositoryServiceProvider extends ServiceProvider {
    public function register() {
        $this->app->bind(OrgRepositoryInterface::class, OrgRepository::class);
        $this->app->bind(WebhookRepositoryInterface::class, WebhookRepository::class);
        $this->app->bind(ApiKeyRepositoryInterface::class, ApiKeyRepository::class);
        $this->app->bind(OrgServiceInterface::class, OrgService::class);
        $this->app->bind(WebhookServiceInterface::class, WebhookService::class);
        $this->app->bind(ApiKeyServiceInterface::class, ApiKeyService::class);
    }
}
