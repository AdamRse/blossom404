<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlantController extends Controller {
    /**
     * Display a listing of all plants.
     */
    public function index(): JsonResponse {
        $plants = Plant::all();
        return response()->json($plants);
    }

    /**
     * Store a newly created plant in storage.
     */
    public function store(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'common_name' => 'required|string|max:255',
            'watering_general_benchmark' => 'required|array',
            'watering_general_benchmark.value' => 'required|string',
            'watering_general_benchmark.unit' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        $plant = Plant::create($request->all());
        return response()->json($plant, 201);
    }

    /**
     * Display the specified plant by name.
     */
    public function show(string $name): JsonResponse {
        $plant = Plant::where('common_name', $name)->first();

        if (!$plant) {
            return response()->json([
                'error' => 'Plant not found'
            ], 404);
        }

        return response()->json($plant);
    }

    /**
     * Remove the specified plant from storage.
     */
    public function destroy(string $id): JsonResponse {
        $plant = Plant::find($id);

        if (!$plant) {
            return response()->json([
                'error' => 'Plant not found'
            ], 404);
        }

        $plant->delete();
        return response()->json(null, 204);
    }

    /**
     * Update plant database from external API.
     * Note: This method will be implemented later when integrating with the external API.
     */
    public function updateFromAPI(): JsonResponse {
        // Cette méthode sera implémentée plus tard lorsque nous intégrerons
        // le service API externe Perenual
        return response()->json([
            'message' => 'Plant database update not yet implemented'
        ]);
    }
}
