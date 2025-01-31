<?php
// app/Repositories/Plant/PlantRepository.php

namespace App\Repositories\Plant;

use App\Interfaces\Repositories\PlantRepositoryInterface;
use App\Models\Plant;
use Illuminate\Database\Eloquent\Collection;

class PlantRepository implements PlantRepositoryInterface {
    public function getAllPlants(): Collection {
        return Plant::all();
    }

    public function getPlantById(int $id): ?Plant {
        return Plant::find($id);
    }

    public function getPlantByCommonName(string $name): ?Plant {
        return Plant::where('common_name', $name)->first();
    }

    public function createOrUpdatePlant(array $plantData): Plant {
        return Plant::updateOrCreate(
            ['perenual_id' => $plantData['perenual_id']],
            $plantData
        );
    }

    public function deletePlant(int $id): bool {
        return Plant::destroy($id) > 0;
    }

    public function existsByPerenualId(int $perenualId): bool {
        return Plant::where('perenual_id', $perenualId)->exists();
    }

    public function bulkCreateOrUpdate(array $plantsData): Collection {
        $plants = new Collection();

        foreach ($plantsData as $plantData) {
            $plants->push($this->createOrUpdatePlant($plantData));
        }

        return $plants;
    }
}
