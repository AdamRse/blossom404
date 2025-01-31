<?php
//app/Interfaces/Services/PerenualApiService.php

namespace App\Services;

use App\Interfaces\PlantApiServiceInterface;
use App\Models\Plant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PerenualApiService implements PlantApiServiceInterface {
    private string $apiKey;
    private string $baseUrl = 'https://perenual.com/api/';

    public function __construct() {
        $this->apiKey = config('services.perenual.key');
    }

    public function fetchAndStoreAllPlants(): void {
        try {
            $page = 1;
            $hasMorePages = true;

            while ($hasMorePages) {
                $response = Http::get($this->baseUrl . 'species-list', [
                    'key' => $this->apiKey,
                    'page' => $page
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    foreach ($data['data'] as $plantData) {
                        $this->processAndStorePlant($plantData);
                    }

                    // Vérifier s'il y a une page suivante
                    $hasMorePages = $data['current_page'] < $data['last_page'];
                    $page++;
                } else {
                    Log::error('Failed to fetch plants from API', [
                        'status' => $response->status(),
                        'response' => $response->json()
                    ]);
                    break;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error while fetching plants', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function fetchPlantByName(string $name): ?array {
        try {
            $response = Http::get($this->baseUrl . 'species-list', [
                'key' => $this->apiKey,
                'q' => $name
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['data'])) {
                    return $this->transformPlantData($data['data'][0]);
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error while fetching plant by name', [
                'name' => $name,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function transformPlantData(array $apiData): array {
        return [
            'perenual_id' => $apiData['id'],
            'common_name' => $apiData['common_name'],
            'scientific_name' => $apiData['scientific_name'] ?? null,
            'description' => $apiData['description'] ?? null,
            'sunlight' => is_array($apiData['sunlight']) ? implode(', ', $apiData['sunlight']) : $apiData['sunlight'],
            'watering' => $apiData['watering'] ?? null,
            'pruning' => isset($apiData['pruning']) ? json_encode($apiData['pruning']) : null,
            'indoor' => isset($apiData['indoor']) ? json_encode($apiData['indoor']) : null,
            'care_level' => $apiData['care_level'] ?? null,
            'maintenance' => isset($apiData['maintenance']) ? json_encode($apiData['maintenance']) : null,
            'growth_rate' => isset($apiData['growth_rate']) ? json_encode($apiData['growth_rate']) : null,
            'care_guides' => $apiData['care_guides'] ?? null,
            'poisonous' => isset($apiData['poisonous']) ? json_encode($apiData['poisonous']) : null,
            'default_image' => $apiData['default_image']['regular_url'] ?? null,
            'watering_general_benchmark' => [
                'value' => $this->extractWateringFrequency($apiData['watering'] ?? ''),
                'unit' => 'days'
            ]
        ];
    }

    private function processAndStorePlant(array $plantData): void {
        $transformedData = $this->transformPlantData($plantData);

        Plant::updateOrCreate(
            ['perenual_id' => $transformedData['perenual_id']],
            $transformedData
        );
    }

    private function extractWateringFrequency(string $wateringDescription): string {
        $frequencies = [
            'frequent' => '3-5',
            'average' => '7-10',
            'minimum' => '10-14',
            'none' => '30'
        ];

        $wateringDescription = strtolower($wateringDescription);
        foreach ($frequencies as $key => $value) {
            if (str_contains($wateringDescription, $key)) {
                return $value;
            }
        }

        return '7-10'; // valeur par défaut
    }
}
