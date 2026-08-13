<?php

namespace App\Providers;

use App\Models\User;
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
        Blade::anonymousComponentPath(
            resource_path('views/ui'),
            'ui'
        );

        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->isRoot()) {
                return true;
            }

            if (! str_contains($ability, ':')) {
                return null;
            }

            return $user->hasPermission($ability);
        });
    }
}
