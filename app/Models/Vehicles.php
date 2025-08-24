<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicles extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'model',
        'year',
        'fuel_type',
        'transmission',
        'engine_capacity',
        'license_plate',
        'tank_capacity',
        'color',
        'notes',
        'is_active',
        'image',
        'initial_odometer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
