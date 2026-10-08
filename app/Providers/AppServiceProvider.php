<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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

        // Gate interceptor
        Gate::before(function ($user, string $ability) {
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability) ?: null;
            }

            return null;
        });

        // Custom Blade directive for permission check
        Blade::if('hasPermission', function (string $permission) {
            $user = Auth::guard('admin')->user() ?? Auth::guard('web')->user();

            return $user && $user->hasPermission($permission);
        });

        // Custom Blade directive for multiple permissions check (OR logic)
        Blade::if('hasAnyPermission', function (string|array $permissions) {
            $user = Auth::guard('admin')->user() ?? Auth::guard('web')->user();

            if (! $user) {
                return false;
            }

            $perms = is_array($permissions) ? $permissions : explode('|', $permissions);

            foreach ($perms as $perm) {
                if ($user->hasPermission(trim($perm))) {
                    return true;
                }
            }

            return false;
        });
    }
}
