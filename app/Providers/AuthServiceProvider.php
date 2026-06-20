<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Define any policies here if you have them, like: 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin', function ($user) {
            return $user->isRole('admin');
        });

        Gate::define('manager', function ($user) {// Log the user type
            return $user->isRole('manager');
        });

        Gate::define('employee', function ($user) {
            return $user->isRole('employee');
        });

        Gate::define('guest', function ($user) {
            return $user->isRole('guest');
        });

        Gate::define('client', function ($user) {
            return $user->isRole('client');
        });
    }
}
