<?php

namespace App\Interfaces\Services\Watering;

use App\Models\Plant;

interface WateringServiceInterface {
    /**
     * Calcule la prochaine date d'arrosage pour une plante
     *
     * @param Plant $plant
     * @param string $city
     * @return \DateTime
     */
    public function calculateNextWateringDate(Plant $plant, string $city): \DateTime;

    /**
     * Programme une notification d'arrosage
     *
     * @param Plant $plant
     * @param \DateTime $wateringDate
     * @return void
     */
    public function scheduleWateringReminder(Plant $plant, \DateTime $wateringDate): void;
}
