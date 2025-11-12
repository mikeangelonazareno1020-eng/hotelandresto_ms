<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportVehicle extends Model
{
    use HasFactory;

    protected $table = 'transport_vehicles';

    protected $fillable = [
        'plate_no',
        'vehicle_type',
        'make',
        'model',
        'color',
        'year',
        'capacity',
        'driver_id',
        'gps_device_id',
        'last_latitude',
        'last_longitude',
        'last_speed',
        'last_heading',
        'last_seen_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'last_latitude' => 'decimal:7',
        'last_longitude' => 'decimal:7',
        'last_speed' => 'decimal:2',
        'last_seen_at' => 'datetime',
    ];

    public function getLastCoordinatesAttribute(): ?string
    {
        if (!is_null($this->last_latitude) && !is_null($this->last_longitude)) {
            return $this->last_latitude . ', ' . $this->last_longitude;
        }
        return null;
    }

    public function driver()
    {
        return $this->belongsTo(Staff::class, 'driver_id');
    }
}
