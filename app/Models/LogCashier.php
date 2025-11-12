<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogCashier extends Model
{
    use HasFactory;

    protected $table = 'logs_cashier';
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'adminId',
        'cashier_name',
        'action_type',
        'reference_id',
        'description',
        'ip_address',
        'device',
        'browser',
        'logged_at',
    ];
}
