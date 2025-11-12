<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportVehiclePath extends Model
{
    protected $table = 'transport_vehicle_paths';

    protected $fillable = [
        'vehicle_id',
        'path',
        'points_count',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'started_at',
        'ended_at',
        'saved_by',
    ];

    protected $casts = [
        'path' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
}

