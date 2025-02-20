<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserPlantController extends Controller {
    /**
     * Display a listing of the user's plants.
     */
    public function index(): JsonResponse {
        try {
            /** @var User $user */
            $user = Auth::user();
            $plants = $user->plants()->with(['users'])->get();

            return response()->json([
                'message' => 'Plants retrieved successfully',
                'data' => $plants
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve plants',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created plant-user relationship.
     */
    public function store(Request $request): JsonResponse {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            'plant_name' => 'required|string|max:255',
            'city' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            // Récupérer la plante par son nom
            $plant = Plant::where('common_name', $request->plant_name)->first();

            if (!$plant) {
                return response()->json([
                    'error' => 'Plant not found',
                    'message' => 'The specified plant does not exist in our database'
                ], 404);
            }

            /** @var User $user */
            $user = Auth::user();

            // Vérifier si l'utilisateur possède déjà cette plante
            if ($user->plants()->where('plant_id', $plant->id)->exists()) {
                return response()->json([
                    'error' => 'Plant already added',
                    'message' => 'You already have this plant in your collection'
                ], 409);
            }

            // Attacher la plante à l'utilisateur
            $user->plants()->attach($plant->id);

            // TODO: Ici, nous devrons plus tard ajouter la logique pour:
            // 1. Récupérer les données météo pour la ville
            // 2. Calculer le prochain arrosage
            // 3. Programmer la notification d'arrosage

            return response()->json([
                'message' => 'Plant added to your collection successfully',
                'data' => $plant
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to add plant',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified plant from user's collection.
     */
    public function destroy(string $id): JsonResponse {
        try {
            /** @var User $user */
            $user = Auth::user();

            // Vérifier si la relation existe
            $exists = $user->plants()->where('user_plant.id', $id)->exists();

            if (!$exists) {
                return response()->json([
                    'error' => 'Relationship not found',
                    'message' => 'This plant is not in your collection'
                ], 404);
            }

            // Supprimer la relation par l'ID de la table pivot
            $user->plants()->wherePivot('id', $id)->detach();

            return response()->json([
                'message' => 'Plant removed from your collection successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to remove plant',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
