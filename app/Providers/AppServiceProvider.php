<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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

        Paginator::defaultView('vendor.pagination.tailwind');
    }
}
