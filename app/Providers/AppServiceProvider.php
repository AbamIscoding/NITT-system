<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('view-logs', function (User $user) {
            return (bool) $user->is_admin;
        });

        Gate::define('manage-users', function (User $user) {
            return (bool) $user->is_admin;
        });
    }
}
