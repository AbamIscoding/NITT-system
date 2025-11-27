<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

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
