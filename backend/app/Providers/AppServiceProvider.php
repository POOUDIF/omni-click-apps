<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MongoDB\Laravel\MongoDBServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind a default null tenant — overwritten per-request by TenantMiddleware
        $this->app->instance('tenant.company_id', null);

        // Register MongoDB service provider (required for mongodb/laravel-mongodb)
        $this->app->register(MongoDBServiceProvider::class);
    }

    public function boot(): void
    {
        //
    }
}
