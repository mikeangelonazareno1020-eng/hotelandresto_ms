<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $table = 'room_amenities_extras';

    protected $fillable = [
        'code', 'name', 'category', 'default_price', 'is_extra',
    ];
}
