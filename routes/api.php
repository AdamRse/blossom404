<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\UserPlantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes d'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes pour la gestion des plantes (sans authentification requise)
Route::get('/plant', [PlantController::class, 'index']); // Récupérer toutes les plantes
Route::post('/plant', [PlantController::class, 'store']); // Ajouter une plante
Route::get('/plant/{name}', [PlantController::class, 'show']); // Récupérer une plante par son nom
Route::delete('/plant/{id}', [PlantController::class, 'destroy']); // Supprimer une plante
Route::post('/plant/update', [PlantController::class, 'updateFromAPI']); // Route pour mettre à jour la base de données depuis l'API externe

// Routes pour la gestion des plantes des utilisateurs (authentification requise)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']); // Se déconnecter
    Route::post('/user/plant', [UserPlantController::class, 'store']); // Ajouter une plante à l'utilisateur
    Route::get('/user/plants', [UserPlantController::class, 'index']); // Récupérer toutes les plantes de l'utilisateur
    Route::delete('/user/plant/{id}', [UserPlantController::class, 'destroy']); // Supprimer une plante de l'utilisateur
});
