<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleLocation extends Model
{
    protected $table = 'transport_vehicle_locations';

    public $timestamps = true;

    protected $fillable = [
        'vehicle_id',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
    ];

    // If you later need relationship:
    // public function vehicle()
    // {
    //     return $this->belongsTo(TransportVehicle::class, 'vehicle_id');
    // }
}
