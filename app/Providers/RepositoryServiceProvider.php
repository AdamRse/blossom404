<?php

namespace App\Providers;

use App\Interfaces\Repositories\PlantRepositoryInterface;
use App\Interfaces\Services\Weather\WeatherServiceInterface;
use App\Interfaces\Services\Watering\WateringServiceInterface;
use App\Repositories\Plant\PlantRepository;
use App\Services\Weather\WeatherService;
use App\Services\Watering\WateringService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(PlantRepositoryInterface::class, PlantRepository::class);
        $this->app->bind(WeatherServiceInterface::class, WeatherService::class);
        $this->app->bind(WateringServiceInterface::class, WateringService::class);
    }
}
