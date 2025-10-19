<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelStation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'brand',
        'cnpj',
        'address',
        'city',
        'state',
        'zip_code',
        'phone',
        'location',
        'latitude',
        'longitude',
        'services',
        'payment_methods',
        'opening_hours',
        'is_active',
        'is_verified',
        'rating_avg',
        'rating_count',
    ];

    protected $casts = [
        'services' => 'array',
        'payment_methods' => 'array',
        'opening_hours' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'rating_avg' => 'decimal:2',
        'rating_count' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function fuelPrices(): HasMany
    {
        return $this->hasMany(FuelPrice::class);
    }

    public function currentPrices(): HasMany
    {
        return $this->hasMany(FuelPrice::class)->where('is_current', true);
    }

    public function priceReports(): HasMany
    {
        return $this->hasMany(PriceReport::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    public function refuels(): HasMany
    {
        return $this->hasMany(Refuel::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_stations');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeNearby($query, float $latitude, float $longitude, float $radiusKm = 10)
    {
        return $query->selectRaw('
            *,
            ST_Distance(
                location::geography,
                ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography
            ) / 1000 as distance_km
        ', [$longitude, $latitude])
            ->whereRaw('
            ST_DWithin(
                location::geography,
                ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography,
                ?
            )
        ', [$longitude, $latitude, $radiusKm * 1000])
            ->orderBy('distance_km');
    }
}
