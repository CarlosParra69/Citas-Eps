<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Definir Gates para control de acceso basado en roles
        Gate::define('superadmin-only', function ($user) {
            return $user->rol === 'superadmin';
        });

        Gate::define('medico-or-superadmin', function ($user) {
            return in_array($user->rol, ['medico', 'superadmin']);
        });

        Gate::define('paciente-or-medico-or-superadmin', function ($user) {
            return in_array($user->rol, ['paciente', 'medico', 'superadmin']);
        });
    }
}
