<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_station_id',
        'fuel_type',
        'price',
        'valid_from',
        'valid_until',
        'is_current',
        'reported_by',
    ];

    protected $casts = [
        'price' => 'decimal:3',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_current' => 'boolean',
    ];

    // Relationships
    public function fuelStation(): BelongsTo
    {
        return $this->belongsTo(FuelStation::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeByFuelType($query, string $fuelType)
    {
        return $query->where('fuel_type', $fuelType);
    }
}
