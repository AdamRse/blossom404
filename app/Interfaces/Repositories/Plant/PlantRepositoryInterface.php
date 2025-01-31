<?php
// app/Interfaces/Repositories/Plant/PlantRepositoryInterface.php

namespace App\Interfaces\Repositories;

use App\Models\Plant;
use Illuminate\Database\Eloquent\Collection;

interface PlantRepositoryInterface {
    /**
     * Récupère toutes les plantes
     *
     * @return Collection
     */
    public function getAllPlants(): Collection;

    /**
     * Récupère une plante par son ID
     *
     * @param int $id
     * @return Plant|null
     */
    public function getPlantById(int $id): ?Plant;

    /**
     * Récupère une plante par son nom commun
     *
     * @param string $name
     * @return Plant|null
     */
    public function getPlantByCommonName(string $name): ?Plant;

    /**
     * Crée ou met à jour une plante
     *
     * @param array $plantData
     * @return Plant
     */
    public function createOrUpdatePlant(array $plantData): Plant;

    /**
     * Supprime une plante
     *
     * @param int $id
     * @return bool
     */
    public function deletePlant(int $id): bool;

    /**
     * Vérifie si une plante existe par son ID Perenual
     *
     * @param int $perenualId
     * @return bool
     */
    public function existsByPerenualId(int $perenualId): bool;

    /**
     * Crée plusieurs plantes en une seule opération
     *
     * @param array $plantsData
     * @return Collection
     */
    public function bulkCreateOrUpdate(array $plantsData): Collection;
}
