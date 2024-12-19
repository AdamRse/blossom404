<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->string('common_name');
            $table->json('watering_general_benchmark');
            $table->timestamps();
        });

        // Création de la table pivot user_plant
        Schema::create('user_plant', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('plant_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('user_plant');
        Schema::dropIfExists('plants');
    }
};
