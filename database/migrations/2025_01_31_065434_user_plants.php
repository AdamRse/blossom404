<?php
// File location in project : database/migrations/2025_01_31_065434_user_plants.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('user_plant', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('plant_id');
            $table->timestamp('last_watered_at')->nullable();
            $table->json('watering_schedule')->nullable()->comment('Personnalized watering schedule for this plant');
            $table->text('personal_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('user_plant');
    }
};
