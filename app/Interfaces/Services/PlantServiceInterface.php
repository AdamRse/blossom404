<?php
//app/Interfaces/Services/PlantServiceInterface.php

namespace App\Interfaces;

interface PlantApiServiceInterface {
    /**
     * Fetch all plants from the API and store them in the database
     */
    public function fetchAndStoreAllPlants(): void;

    /**
     * Fetch a specific plant by its name
     * @param string $name
     * @return array|null
     */
    public function fetchPlantByName(string $name): ?array;

    /**
     * Transform API data to match our database structure
     * @param array $apiData
     * @return array
     */
    public function transformPlantData(array $apiData): array;
}
