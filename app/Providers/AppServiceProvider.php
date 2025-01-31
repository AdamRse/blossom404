<?php
//app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Interfaces\PerenualApiServiceInterface;
use App\Services\PerenualApiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->app->bind(PerenualApiServiceInterface::class, PerenualApiService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        //
    }
}
