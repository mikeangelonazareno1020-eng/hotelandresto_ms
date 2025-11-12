<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiDevice extends Model
{
    protected $fillable = [
        'name',
        'uid',
        'api_key_hash',
        'is_active',
        'last_used_at',
        'last_ip',
    ];
}

