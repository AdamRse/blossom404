<?php
//database/migrations/2024_12_19_150042_create_plants_table.php

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
            $table->integer('perenual_id')->nullable();
            $table->string('common_name');
            $table->string('scientific_name')->nullable();
            $table->text('description')->nullable();
            $table->string('sunlight')->nullable();
            $table->string('watering')->nullable();
            $table->json('watering_general_benchmark');
            $table->json('pruning')->nullable();
            $table->json('indoor')->nullable();
            $table->string('care_level')->nullable();
            $table->json('maintenance')->nullable();
            $table->json('growth_rate')->nullable();
            $table->string('care_guides')->nullable();
            $table->json('poisonous')->nullable();
            $table->string('default_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('plants');
    }
};
