<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Jhaoda\LaravelPostgis\Eloquent\PostgisTrait;

class PriceReport extends Model
{
    use HasFactory, PostgisTrait;

    protected $fillable = [
        'fuel_station_id',
        'user_id',
        'fuel_type',
        'price',
        'photo_url',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'location',
    ];

    protected $casts = [
        'price' => 'decimal:3',
        'reviewed_at' => 'datetime',
    ];

    protected $postgisFields = [
        'location',
    ];

    // Relationships
    public function fuelStation(): BelongsTo
    {
        return $this->belongsTo(FuelStation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
