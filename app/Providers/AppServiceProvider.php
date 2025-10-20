<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repositories
        $this->app->bind(
            \App\Repositories\GameRepositoryInterface::class,
            \App\Repositories\GameRepository::class
        );

        // Enable query cache in production
        if (app()->environment('production')) {
            // Default string length for database columns
            Schema::defaultStringLength(191);

            // Disable debug mode in production
            config(['app.debug' => false]);

            // Set a longer cache lifetime for database queries
            config(['database.cache.ttl' => 60 * 60]); // 1 hour

            // Enable route caching in production
            $this->enableRouteCaching();
        }
    }

    /**
     * Enable route caching in production
     */
    private function enableRouteCaching(): void
    {
        $this->app->booted(function () {
            // For production, we recommend running these commands manually during deployment:
            // php artisan route:cache
            // php artisan config:cache
            // php artisan view:cache
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('is-admin', function (User $user) {
            return $user->role === Role::ADMIN;
        });

        Gate::define('is-publisher', function (User $user) {
            return $user->role === Role::PUBLISHER;
        });

        Gate::define('is-user', function (User $user) {
            return $user->role === Role::USER;
        });

        // Set default pagination view
        Paginator::defaultView('vendor.pagination.tailwind');

        // Production optimizations
        if (app()->environment('production')) {
            // Disable lazy loading in production for better performance
            Model::preventLazyLoading(true);

            // Enable strict mode for better database interactions
            Model::shouldBeStrict(!app()->isProduction());

            // Set a reasonable string length for MySQL utf8mb4 compatibility
            Schema::defaultStringLength(191);
        }
    }
}
