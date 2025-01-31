<?php
//app/Models/Plant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plant extends Model {
    use HasFactory;

    protected $fillable = [
        'common_name',
        'watering_general_benchmark',
    ];

    protected $casts = [
        'watering_general_benchmark' => 'json',
    ];

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'user_plant')
            ->withTimestamps();
    }
}
