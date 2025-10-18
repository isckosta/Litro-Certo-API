<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refuel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fuel_station_id',
        'fuel_type',
        'liters',
        'price_per_liter',
        'total_amount',
        'odometer',
        'full_tank',
        'notes',
        'refueled_at',
    ];

    protected $casts = [
        'liters' => 'decimal:3',
        'price_per_liter' => 'decimal:3',
        'total_amount' => 'decimal:2',
        'odometer' => 'integer',
        'full_tank' => 'boolean',
        'refueled_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fuelStation(): BelongsTo
    {
        return $this->belongsTo(FuelStation::class);
    }
}
