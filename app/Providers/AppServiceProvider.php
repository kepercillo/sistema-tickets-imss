<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public const HOME = '/soporte/tickets'; // O la ruta correspondiente: route('soporte.tickets.index')
    
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive(); // <-- 2. Indicar uso de Bootstrap 5
        app()->setLocale('es');
    }
}
