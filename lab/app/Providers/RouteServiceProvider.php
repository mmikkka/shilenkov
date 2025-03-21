<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot() : void
    {
        // Загружаем маршруты API
        Route::prefix('api') // <-- Все API-роуты будут начинаться с /api
        ->middleware('api')
            ->group(base_path('routes/api.php'));

        // Загружаем маршруты Web
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}
