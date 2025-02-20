<?php

namespace App\Services\Watering;

use App\Interfaces\Services\Watering\WateringServiceInterface;
use App\Interfaces\Services\Weather\WeatherServiceInterface;
use App\Models\Plant;
use App\Notifications\WateringReminder;
use Illuminate\Support\Facades\Log;

class WateringService implements WateringServiceInterface {
    private WeatherServiceInterface $weatherService;

    public function __construct(WeatherServiceInterface $weatherService) {
        $this->weatherService = $weatherService;
    }

    public function calculateNextWateringDate(Plant $plant, string $city): \DateTime {
        try {
            // Récupérer les données météo
            $weatherData = $this->weatherService->getWeatherForCity($city);

            // Obtenir le facteur d'ajustement basé sur la météo
            $weatherAdjustment = $weatherData ?
                $this->weatherService->calculateWateringAdjustment($weatherData) :
                1.0;

            // Extraire l'intervalle d'arrosage de base de la plante
            $wateringBenchmark = $plant->watering_general_benchmark;
            $interval = explode('-', $wateringBenchmark['value']);

            // Calculer la moyenne des jours
            $baseInterval = (intval($interval[0]) + intval($interval[1])) / 2;

            // Appliquer l'ajustement météo
            $adjustedInterval = round($baseInterval * $weatherAdjustment);

            // Calculer la prochaine date d'arrosage
            $nextWatering = new \DateTime();
            $nextWatering->modify("+{$adjustedInterval} days");

            return $nextWatering;
        } catch (\Exception $e) {
            Log::error('Error calculating next watering date', [
                'plant_id' => $plant->id,
                'city' => $city,
                'error' => $e->getMessage()
            ]);

            // En cas d'erreur, utiliser l'intervalle par défaut
            $nextWatering = new \DateTime();
            $nextWatering->modify('+7 days');

            return $nextWatering;
        }
    }

    public function scheduleWateringReminder(Plant $plant, \DateTime $wateringDate): void {
        try {
            foreach ($plant->users as $user) {
                // Créer et programmer la notification
                $user->notify((new WateringReminder($plant, $wateringDate))
                    ->delay($wateringDate));
            }

            Log::info('Watering reminder scheduled', [
                'plant_id' => $plant->id,
                'watering_date' => $wateringDate->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            Log::error('Error scheduling watering reminder', [
                'plant_id' => $plant->id,
                'watering_date' => $wateringDate->format('Y-m-d H:i:s'),
                'error' => $e->getMessage()
            ]);
        }
    }
}
