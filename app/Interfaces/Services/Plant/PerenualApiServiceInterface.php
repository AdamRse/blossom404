<?php
//app/Interfaces/Services/Plant/PerenualApiServiceInterface.php

namespace App\Interfaces;

/**
 * Interface for plant API services
 * @OA\Schema(
 *     schema="PlantApiService",
 *     description="Interface for interacting with external plant APIs"
 * )
 */
interface PerenualApiServiceInterface {
    /**
     * Fetch all plants from the API and store them in the database
     * @OA\Post(
     *     path="/plant/update",
     *     summary="Update local plant database from external API",
     *     tags={"Plants"},
     *     @OA\Response(
     *         response=200,
     *         description="Plants fetched and stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plant database updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error occurred while fetching plants",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function fetchAndStoreAllPlants(): void;

    /**
     * Fetch a specific plant by its name
     * @OA\Get(
     *     path="/plant/{name}",
     *     summary="Get plant information by name",
     *     tags={"Plants"},
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         required=true,
     *         description="Common name of the plant",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plant found",
     *         @OA\JsonContent(ref="#/components/schemas/Plant")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Plant not found")
     *         )
     *     )
     * )
     * @param string $name
     * @return array|null
     */
    public function fetchPlantByName(string $name): ?array;

    /**
     * Fetch detailed information about a specific plant by its Perenual ID
     * @OA\Get(
     *     path="/plant/details/{perenualId}",
     *     summary="Get detailed plant information by Perenual ID",
     *     tags={"Plants"},
     *     @OA\Parameter(
     *         name="perenualId",
     *         in="path",
     *         required=true,
     *         description="Perenual ID of the plant",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plant details found",
     *         @OA\JsonContent(ref="#/components/schemas/PlantDetail")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant details not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Plant details not found")
     *         )
     *     )
     * )
     * @param int $perenualId
     * @return array|null
     */
    public function fetchPlantDetails(int $perenualId): ?array;

    /**
     * Fetch care guides for a specific plant
     * @OA\Get(
     *     path="/plant/{perenualId}/care-guides",
     *     summary="Get plant care guides",
     *     tags={"Plants"},
     *     @OA\Parameter(
     *         name="perenualId",
     *         in="path",
     *         required=true,
     *         description="Perenual ID of the plant",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Care guides found",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CareGuide")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Care guides not found"
     *     )
     * )
     * @param int $perenualId
     * @return array|null
     */
    public function fetchPlantCareGuides(int $perenualId): ?array;

    /**
     * Fetch list of common plant diseases
     * @OA\Get(
     *     path="/plant/diseases",
     *     summary="Get list of plant diseases",
     *     tags={"Plants"},
     *     @OA\Response(
     *         response=200,
     *         description="List of plant diseases",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PlantDisease")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error fetching plant diseases"
     *     )
     * )
     * @return array|null
     */
    public function fetchPlantDiseases(): ?array;

    /**
     * Transform raw API data into our application's format
     * @param array $apiData Raw data from the API
     * @return array Transformed data
     */
    public function transformPlantData(array $apiData): array;
}
