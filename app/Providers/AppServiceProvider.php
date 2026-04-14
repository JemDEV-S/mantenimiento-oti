<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // El rol admin tiene acceso total sin necesitar cada permiso asignado
        Gate::before(function ($user, string $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });
    }
}
