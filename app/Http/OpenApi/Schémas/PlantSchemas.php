<?php
//app/Http/OpenApi/Schemas/PlantSchemas.php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Plant",
 *     required={"id", "common_name", "watering_general_benchmark"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="perenual_id", type="integer"),
 *     @OA\Property(property="common_name", type="string"),
 *     @OA\Property(property="scientific_name", type="string", nullable=true),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="sunlight", type="string", nullable=true),
 *     @OA\Property(property="watering", type="string", nullable=true),
 *     @OA\Property(
 *         property="watering_general_benchmark",
 *         type="object",
 *         @OA\Property(property="value", type="string"),
 *         @OA\Property(property="unit", type="string")
 *     )
 * )
 */
class PlantSchema {
}

/**
 * @OA\Schema(
 *     schema="CareGuide",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="content", type="string")
 * )
 */
class CareGuideSchema {
}

/**
 * @OA\Schema(
 *     schema="PlantDisease",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string")
 * )
 */
class PlantDiseaseSchema {
}
