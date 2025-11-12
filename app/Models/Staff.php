<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';

    protected $fillable = [
        'staffId',
        'firstName',
        'middleName',
        'lastName',
        'gender',
        'dob',
        'phone',
        'email',
        'region',
        'province',
        'city',
        'barangay',
        'street',
        'department',
        'role',
        'status',
    ];
}
