<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // High-level abilities for RBAC
        Gate::define('users.read', fn(User $u) => $u->hasAnyRole(['owner','admin','auditor']));
        Gate::define('users.manage', fn(User $u) => $u->hasAnyRole(['owner','admin']));
        Gate::define('analytics.read', fn(User $u) => $u->hasAnyRole(['owner','admin','auditor']));
    }
}
