<?php
//app/Services/Plant/PerenualApiService.php

namespace App\Services;

use App\Interfaces\PerenualApiServiceInterface;
use App\Interfaces\Repositories\PlantRepositoryInterface;
use App\Models\Plant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PerenualApiService implements PerenualApiServiceInterface {
    private string $apiKey;
    private string $baseUrl = 'https://perenual.com/api/';
    private const API_VERSION = 'v1';
    private const DEFAULT_TIMEOUT = 30;
    private \Illuminate\Http\Client\PendingRequest $http;
    private PlantRepositoryInterface $plantRepository;

    public function __construct(PlantRepositoryInterface $plantRepository) {
        $this->plantRepository = $plantRepository;
        $this->apiKey = config('services.perenual.key');
        $this->http = Http::timeout(self::DEFAULT_TIMEOUT)
            ->baseUrl($this->baseUrl)
            ->withQueryParameters(['key' => $this->apiKey]);
    }

    public function fetchAndStoreAllPlants(): void {
        try {
            $page = 1;
            $hasMorePages = true;
            $plantsData = [];

            while ($hasMorePages) {
                $response = $this->http->get('species-list', [
                    'page' => $page
                ]);

                if (!$response->successful()) {
                    Log::error('Failed to fetch plants from API', [
                        'status' => $response->status(),
                        'response' => $response->json()
                    ]);
                    throw new \Exception('Failed to fetch plants from API: ' . $response->status());
                }

                $data = $response->json();

                // Traitement par lots de 10 plantes pour éviter la surcharge mémoire
                foreach (array_chunk($data['data'], 10) as $plantChunk) {
                    $detailedPlants = [];

                    foreach ($plantChunk as $plantData) {
                        try {
                            $details = $this->fetchPlantDetails($plantData['id']);
                            if ($details) {
                                $detailedPlants[] = $details;
                            }
                        } catch (\Exception $e) {
                            Log::warning('Failed to fetch details for plant', [
                                'plant_id' => $plantData['id'],
                                'error' => $e->getMessage()
                            ]);
                            continue;
                        }
                    }

                    // Stockage du lot de plantes
                    if (!empty($detailedPlants)) {
                        $this->plantRepository->bulkCreateOrUpdate($detailedPlants);
                    }
                }

                // Vérification s'il y a d'autres pages
                $hasMorePages = $data['current_page'] < $data['last_page'];
                $page++;

                // Petit délai pour éviter de surcharger l'API
                if ($hasMorePages) {
                    usleep(500000); // 500ms de délai
                }
            }

            Log::info('Plant database update completed successfully');
        } catch (\Exception $e) {
            Log::error('Error while fetching plants', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Failed to update plant database: ' . $e->getMessage());
        }
    }

    public function fetchPlantByName(string $name): ?array {
        try {
            $response = $this->http->get('species-list', [
                'q' => $name
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['data'])) {
                    // Récupérer les détails complets de la première plante trouvée
                    return $this->fetchPlantDetails($data['data'][0]['id']);
                }
            }

            Log::warning('Plant not found', [
                'name' => $name,
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error while fetching plant by name', [
                'name' => $name,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function fetchPlantDiseases(): ?array {
        try {
            $response = $this->http->get('pest-disease-list');

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Failed to fetch plant diseases', [
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching plant diseases', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function fetchPlantDetails(int $perenualId): ?array {
        try {
            $response = $this->http->get("species/details/{$perenualId}");

            if ($response->successful()) {
                $data = $response->json();
                return $this->transformPlantData($data);
            }

            Log::warning('Failed to fetch plant details', [
                'id' => $perenualId,
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching plant details', [
                'id' => $perenualId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function fetchPlantCareGuides(int $perenualId): ?array {
        try {
            $response = $this->http->get('species-care-guide-list', [
                'species_id' => $perenualId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Failed to fetch plant care guides', [
                'id' => $perenualId,
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error fetching plant care guides', [
                'id' => $perenualId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    // Amélioration de la méthode transformPlantData
    public function transformPlantData(array $apiData): array {
        $careGuides = $this->fetchPlantCareGuides($apiData['id'] ?? null);

        return [
            'perenual_id' => $apiData['id'],
            'common_name' => $apiData['common_name'],
            'scientific_name' => $apiData['scientific_name'] ?? null,
            'description' => $apiData['description'] ?? null,
            'sunlight' => is_array($apiData['sunlight'])
                ? implode(', ', $apiData['sunlight'])
                : $apiData['sunlight'],
            'watering' => $apiData['watering'] ?? null,
            'pruning' => $apiData['pruning'] ?? null,
            'indoor' => $apiData['indoor'] ?? null,
            'care_level' => $apiData['care_level'] ?? null,
            'maintenance' => $apiData['maintenance'] ?? null,
            'growth_rate' => $apiData['growth_rate'] ?? null,
            'care_guides' => $careGuides ? json_encode($careGuides) : null,
            'poisonous' => $apiData['poisonous'] ?? null,
            'default_image' => $apiData['default_image']['regular_url'] ?? null,
            'watering_general_benchmark' => [
                'value' => $this->extractWateringFrequency($apiData['watering'] ?? ''),
                'unit' => 'days'
            ]
        ];
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

        return '7-10';
    }
}
