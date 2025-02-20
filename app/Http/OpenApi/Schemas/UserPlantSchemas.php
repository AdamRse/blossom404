<?php
// File location in project : app/Http/OpenApi/Schemas/UserPlantSchemas.php

namespace App\Http\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="UserPlant",
 *     description="User's plant with additional information",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="pivot", ref="#/components/schemas/UserPlantPivot"),
 *     @OA\Property(property="plant", ref="#/components/schemas/Plant")
 * )
 */
class UserPlantSchema {
}

/**
 * @OA\Schema(
 *     schema="UserPlantPivot",
 *     description="Pivot information for user's plant",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="plant_id", type="integer"),
 *     @OA\Property(property="last_watered_at", type="string", format="datetime", nullable=true),
 *     @OA\Property(
 *         property="watering_schedule",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="frequency", type="integer", example=3),
 *         @OA\Property(property="unit", type="string", example="days"),
 *         @OA\Property(property="preferred_time", type="string", example="08:00")
 *     ),
 *     @OA\Property(property="personal_notes", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="datetime"),
 *     @OA\Property(property="updated_at", type="string", format="datetime")
 * )
 */
class UserPlantPivotSchema {
}

/**
 * @OA\Schema(
 *     schema="UserPlantResponse",
 *     description="Response when adding a plant to user's collection",
 *     @OA\Property(property="message", type="string", example="Plant added to your collection successfully"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="plant", ref="#/components/schemas/Plant"),
 *         @OA\Property(property="next_watering", type="string", format="datetime"),
 *         @OA\Property(
 *             property="watering_schedule",
 *             type="object",
 *             @OA\Property(property="frequency", type="integer", example=3),
 *             @OA\Property(property="unit", type="string", example="days"),
 *             @OA\Property(property="preferred_time", type="string", example="08:00")
 *         ),
 *         @OA\Property(property="personal_notes", type="string", example="Near the living room window")
 *     )
 * )
 */
class UserPlantResponseSchema {
}
