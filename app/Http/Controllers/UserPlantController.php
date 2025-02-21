<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\Watering\WateringServiceInterface;
use App\Models\Plant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="User Plants",
 *     description="API Endpoints for managing user's plant collection"
 * )
 */
class UserPlantController extends Controller {
    private WateringServiceInterface $wateringService;

    public function __construct(WateringServiceInterface $wateringService) {
        $this->wateringService = $wateringService;
    }

    /**
     * @OA\Get(
     *     path="/user/plants",
     *     summary="Get user's plant collection",
     *     tags={"User Plants"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of user's plants",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plants retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/UserPlant")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(): JsonResponse {
        try {
            /** @var User $user */
            $user = Auth::user();
            $plants = $user->plants()
                ->with(['users'])
                ->withPivot(['last_watered_at', 'watering_schedule', 'personal_notes'])
                ->get();

            return response()->json([
                'message' => 'Plants retrieved successfully',
                'data' => $plants
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve plants', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'error' => 'Failed to retrieve plants',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/user/plant",
     *     summary="Add a plant to user's collection",
     *     tags={"User Plants"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plant_name", "city"},
     *             @OA\Property(property="plant_name", type="string", example="Monstera"),
     *             @OA\Property(property="city", type="string", example="Paris"),
     *             @OA\Property(
     *                 property="watering_schedule",
     *                 type="object",
     *                 example={"frequency": 3, "unit": "days", "preferred_time": "08:00"}
     *             ),
     *             @OA\Property(property="personal_notes", type="string", example="Near the living room window")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plant added successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserPlantResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Plant already in collection"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'plant_name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'watering_schedule' => 'nullable|json',
            'personal_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $plant = Plant::where('common_name', $request->plant_name)->first();

            if (!$plant) {
                return response()->json([
                    'error' => 'Plant not found',
                    'message' => 'The specified plant does not exist in our database'
                ], 404);
            }

            /** @var User $user */
            $user = Auth::user();

            if ($user->plants()->where('plant_id', $plant->id)->exists()) {
                return response()->json([
                    'error' => 'Plant already added',
                    'message' => 'You already have this plant in your collection'
                ], 409);
            }

            // Préparer les données pivot
            $pivotData = [
                'city' => $request->city,
                'watering_schedule' => $request->watering_schedule,
                'personal_notes' => $request->personal_notes
            ];

            // Attacher la plante à l'utilisateur avec les données pivot
            $user->plants()->attach($plant->id, $pivotData);

            // Calculer la prochaine date d'arrosage
            $nextWateringDate = $this->wateringService->calculateNextWateringDate($plant, $request->city);

            // Programmer la notification d'arrosage
            $this->wateringService->scheduleWateringReminder($plant, $nextWateringDate);

            return response()->json([
                'message' => 'Plant added to your collection successfully',
                'data' => [
                    'plant' => $plant,
                    'city' => $request->city,
                    'next_watering' => $nextWateringDate->format('Y-m-d H:i:s'),
                    'watering_schedule' => $request->watering_schedule,
                    'personal_notes' => $request->personal_notes
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to add plant', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'plant_name' => $request->plant_name
            ]);

            return response()->json([
                'error' => 'Failed to add plant',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/user/plant/{id}",
     *     summary="Update plant information in user's collection",
     *     tags={"User Plants"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User plant pivot ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="watering_schedule",
     *                 type="object",
     *                 example={"frequency": 3, "unit": "days", "preferred_time": "08:00"}
     *             ),
     *             @OA\Property(property="personal_notes", type="string", example="Updated location: kitchen")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plant information updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found in user's collection"
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse {
        $validator = Validator::make($request->all(), [
            'watering_schedule' => 'nullable|json',
            'personal_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            /** @var User $user */
            $user = Auth::user();
            $userPlant = $user->plants()->wherePivot('id', $id)->first();

            if (!$userPlant) {
                return response()->json([
                    'error' => 'Plant not found',
                    'message' => 'This plant is not in your collection'
                ], 404);
            }

            $updateData = [];
            if ($request->has('watering_schedule')) {
                $updateData['watering_schedule'] = $request->watering_schedule;
            }
            if ($request->has('personal_notes')) {
                $updateData['personal_notes'] = $request->personal_notes;
            }

            $user->plants()->updateExistingPivot($userPlant->id, $updateData);

            return response()->json([
                'message' => 'Plant information updated successfully',
                'data' => $updateData
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to update plant information', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'plant_pivot_id' => $id
            ]);

            return response()->json([
                'error' => 'Failed to update plant information',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/user/plant/{id}/water",
     *     summary="Record a watering event for a plant",
     *     tags={"User Plants"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User plant pivot ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Watering recorded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Watering recorded successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="last_watered_at", type="string", format="datetime"),
     *                 @OA\Property(property="next_watering", type="string", format="datetime")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found in user's collection"
     *     )
     * )
     */
    public function recordWatering(string $id): JsonResponse {
        try {
            /** @var User $user */
            $user = Auth::user();
            $userPlant = $user->plants()
                ->wherePivot('id', $id)
                ->withPivot(['city'])
                ->first();

            if (!$userPlant) {
                return response()->json([
                    'error' => 'Plant not found',
                    'message' => 'This plant is not in your collection'
                ], 404);
            }

            $now = Carbon::now();
            $user->plants()->updateExistingPivot($userPlant->id, [
                'last_watered_at' => $now
            ]);

            // Calculer la prochaine date d'arrosage basée sur le planning personnalisé ou le planning par défaut
            $nextWateringDate = $this->wateringService->calculateNextWateringDate(
                $userPlant,
                $userPlant->pivot->city ?? 'Paris' // Utilise la ville stockée dans la pivot ou Paris par défaut
            );

            return response()->json([
                'message' => 'Watering recorded successfully',
                'data' => [
                    'last_watered_at' => $now->format('Y-m-d H:i:s'),
                    'next_watering' => $nextWateringDate->format('Y-m-d H:i:s')
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to record watering', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'plant_pivot_id' => $id
            ]);

            return response()->json([
                'error' => 'Failed to record watering',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/user/plant/{id}",
     *     summary="Remove a plant from user's collection",
     *     tags={"User Plants"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User plant pivot ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plant removed successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found in user's collection"
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse {
        try {
            /** @var User $user */
            $user = Auth::user();

            $exists = $user->plants()->where('user_plant.id', $id)->exists();

            if (!$exists) {
                return response()->json([
                    'error' => 'Relationship not found',
                    'message' => 'This plant is not in your collection'
                ], 404);
            }

            $user->plants()->wherePivot('id', $id)->detach();

            return response()->json([
                'message' => 'Plant removed from your collection successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to remove plant', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'relationship_id' => $id
            ]);

            return response()->json([
                'error' => 'Failed to remove plant',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
