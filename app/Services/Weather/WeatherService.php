<?php

namespace App\Services\Weather;

use App\Interfaces\Services\Weather\WeatherServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService implements WeatherServiceInterface {
    private string $apiKey;
    private string $baseUrl = 'http://api.weatherapi.com/v1';
    private const CACHE_DURATION = 7200; // 2 heures en secondes
    private const DEFAULT_TIMEOUT = 30;

    public function __construct() {
        $this->apiKey = config('services.weatherapi.key');
    }

    public function getWeatherForCity(string $city): ?array {
        $cacheKey = $this->getCacheKey($city);

        // Vérifier si les données sont en cache
        if ($this->hasValidCache($city)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout(self::DEFAULT_TIMEOUT)
                ->get("{$this->baseUrl}/current.json", [
                    'key' => $this->apiKey,
                    'q' => $city,
                    'aqi' => 'no'
                ]);

            if (!$response->successful()) {
                Log::error('Failed to fetch weather data', [
                    'city' => $city,
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return null;
            }

            $weatherData = $response->json();

            // Transformer les données en format utile pour notre application
            $transformedData = $this->transformWeatherData($weatherData);

            // Mettre en cache pour 2 heures
            Cache::put($cacheKey, $transformedData, self::CACHE_DURATION);

            return $transformedData;
        } catch (\Exception $e) {
            Log::error('Error fetching weather data', [
                'city' => $city,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function hasValidCache(string $city): bool {
        return Cache::has($this->getCacheKey($city));
    }

    public function calculateWateringAdjustment(array $weatherData): float {
        $adjustment = 1.0;

        // Ajuster en fonction de la température
        if ($weatherData['temperature'] > 30) {
            $adjustment *= 1.3; // Arrosage plus fréquent par temps très chaud
        } elseif ($weatherData['temperature'] < 15) {
            $adjustment *= 0.8; // Arrosage moins fréquent par temps frais
        }

        // Ajuster en fonction de l'humidité
        if ($weatherData['humidity'] > 80) {
            $adjustment *= 0.7; // Réduire l'arrosage si très humide
        } elseif ($weatherData['humidity'] < 40) {
            $adjustment *= 1.2; // Augmenter l'arrosage si très sec
        }

        // Ajuster en fonction des précipitations
        if ($weatherData['precipitation'] > 0) {
            $adjustment *= 0.5; // Réduire significativement si il pleut
        }

        return round($adjustment, 2);
    }

    private function transformWeatherData(array $apiData): array {
        return [
            'temperature' => $apiData['current']['temp_c'],
            'humidity' => $apiData['current']['humidity'],
            'precipitation' => $apiData['current']['precip_mm'],
            'condition' => $apiData['current']['condition']['text'],
            'wind_speed' => $apiData['current']['wind_kph'],
            'last_updated' => $apiData['current']['last_updated'],
        ];
    }

    private function getCacheKey(string $city): string {
        return "weather_data_" . strtolower(str_replace(' ', '_', $city));
    }
}
