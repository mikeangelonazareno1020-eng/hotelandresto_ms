<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportService extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'transport_services';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'reservation_id',
        'passenger_type',
        'group_quantity',
        'luggage',
        'transport_type',
        'pickup_location',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'pickup_eta',
        'transport_rate',
        'service_status',
        'driver_name',
        'vehicle_plate',
        'vehicle_type',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'luggage' => 'array',
        'pickup_latitude' => 'decimal:7',
        'pickup_longitude' => 'decimal:7',
        'pickup_eta' => 'datetime',
        'transport_rate' => 'decimal:2',
    ];

    /**
     * Relationship: belongs to RoomReservation
     */
    public function reservation()
    {
        return $this->belongsTo(RoomReservation::class, 'reservation_id', 'reservation_id');
    }

    /**
     * Accessor: Get formatted pickup coordinates
     */
    public function getPickupCoordinatesAttribute(): ?string
    {
        if ($this->pickup_latitude && $this->pickup_longitude) {
            return "{$this->pickup_latitude}, {$this->pickup_longitude}";
        }
        return null;
    }

    /**
     * Accessor: Check if service is active
     */
    public function getIsActiveAttribute(): bool
    {
        return in_array($this->service_status, ['Pending', 'Confirmed', 'In Transit']);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('service_status', $status);
    }

    /**
     * Scope: Filter by driver
     */
    public function scopeDriver($query, $name)
    {
        return $query->where('driver_name', 'LIKE', "%{$name}%");
    }
}
