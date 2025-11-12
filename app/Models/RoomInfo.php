<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoomInfo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'room_info';

    /**
     * Fillable attributes for mass assignment.
     */
    protected $fillable = [
        'room_number',
        'room_type',
        'room_floor',
        'room_description',
        'room_status',
        'bed_type',
        'max_occupancy',
        'room_amenities',
        'room_rate',
        'room_reservations',
        'dining_table',
        'bathroom',
        'kitchen',
        'ratings',
        'reservation_id',
    ];

    /**
     * Attribute casting for JSON and data types.
     */
    protected $casts = [
        'bed_type' => 'array',
        'room_amenities' => 'array',
        'room_reservations' => 'array',
        'dining_table' => 'array',
        'bathroom' => 'array',
        'kitchen' => 'array',
        'ratings' => 'array',
    ];

    /**
     * Relationship: Room belongs to a specific reservation.
     * (Latest or linked reservation)
     */
    public function reservation()
    {
        return $this->belongsTo(RoomReservation::class, 'reservation_id', 'reservation_id');
    }

    /**
     * Relationship: Room can have many reservations historically.
     */
    public function reservations()
    {
        return $this->hasMany(RoomReservation::class, 'room_number', 'room_number');
    }

    /**
     * Relationship: Many amenities assigned to this room via pivot.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_amenity', 'room_id', 'amenity_id')
            ->withTimestamps();
    }

    /**
     * Accessor: Amenity names derived from relation (fallback to column).
     */
    public function getAmenityNamesAttribute(): array
    {
        if ($this->relationLoaded('amenities')) {
            return $this->amenities->pluck('name')->all();
        }
        return (array) ($this->room_amenities ?? []);
    }

    /**
     * Accessor: Check if the room is currently available.
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->room_status === 'Vacant';
    }

    /**
     * Accessor: Display a formatted room rate with peso symbol.
     */
    public function getFormattedRateAttribute(): string
    {
        return '₱' . number_format($this->room_rate, 2);
    }

    /**
     * Scope: Filter by room type (Standard, Matrimonial, Family Room).
     */
    public function scopeType($query, string $type)
    {
        return $query->where('room_type', $type);
    }

    /**
     * Scope: Filter by status (Vacant, Checked In, etc.)
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('room_status', $status);
    }
}
