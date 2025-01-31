<?php
//app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Interfaces\PlantApiServiceInterface;
use App\Services\PerenualApiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->app->bind(PlantApiServiceInterface::class, PerenualApiService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        //
    }
}
