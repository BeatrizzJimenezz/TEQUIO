<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{

    // Registrar cualquier servicio de la aplicación.
    public function register(): void
    {
        //
    }

    // Bootstrap cualquier servicio de la aplicación.
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}