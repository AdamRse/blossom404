<?php

namespace App\Providers;

use App\Interfaces\Services\Plant\PerenualApiServiceInterface;
use App\Services\Plant\PerenualApiService;
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
