<?php
//app/Models/Plant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plant extends Model {
    use HasFactory;

    protected $fillable = [
        'perenual_id',
        'common_name',
        'scientific_name',
        'description',
        'sunlight',
        'watering',
        'pruning',
        'indoor',
        'care_level',
        'maintenance',
        'growth_rate',
        'care_guides',
        'poisonous',
        'default_image',
        'watering_general_benchmark',
    ];

    protected $casts = [
        'watering_general_benchmark' => 'json',
        'pruning' => 'json',
        'indoor' => 'json',
        'maintenance' => 'json',
        'growth_rate' => 'json',
        'poisonous' => 'json',
    ];

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'user_plant')
            ->withTimestamps();
    }
}
