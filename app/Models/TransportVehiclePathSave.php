<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportVehiclePathSave extends Model
{
    protected $table = 'transport_vehicle_path_saves';

    protected $fillable = [
        'vehicle_id',
        'name',
        'path',
        'points_count',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'started_at',
        'ended_at',
        'vehicle_snapshot',
        'saved_by',
    ];

    protected $casts = [
        'path' => 'array',
        'vehicle_snapshot' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
}

