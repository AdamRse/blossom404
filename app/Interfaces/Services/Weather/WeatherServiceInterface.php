<?php

namespace App\Interfaces\Services\Weather;

interface WeatherServiceInterface {
    /**
     * Récupère les données météorologiques pour une ville donnée
     *
     * @param string $city Nom de la ville
     * @return array|null Données météo ou null si non trouvé
     */
    public function getWeatherForCity(string $city): ?array;

    /**
     * Vérifie si les données météo en cache sont valides pour une ville
     *
     * @param string $city Nom de la ville
     * @return bool
     */
    public function hasValidCache(string $city): bool;

    /**
     * Retourne les facteurs d'ajustement d'arrosage basés sur la météo
     *
     * @param array $weatherData Données météo
     * @return float Facteur d'ajustement (1.0 = normal, >1 = plus fréquent, <1 = moins fréquent)
     */
    public function calculateWateringAdjustment(array $weatherData): float;
}
