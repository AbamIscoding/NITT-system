<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('create-user', function (User $user) {
            return $user->is_admin === 1;
        });

        Gate::define('register', function (User $user) {
            return $user->is_admin === 1;
        });
    }
}
