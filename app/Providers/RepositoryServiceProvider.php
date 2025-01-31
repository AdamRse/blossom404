<?php
// app/Providers/RepositoryServiceProvider.php

namespace App\Providers;

use App\Interfaces\Repositories\PlantRepositoryInterface;
use App\Repositories\Plant\PlantRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(PlantRepositoryInterface::class, PlantRepository::class);
    }
}
