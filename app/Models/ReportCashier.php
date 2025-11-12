<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCashier extends Model
{
    use HasFactory;

    protected $table = 'reports_cashier';
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'adminId',
        'cashier_name',
        'report_date',
        'total_orders',
        'total_sales',
        'total_cash',
        'total_card',
        'total_refund',
        'total_discount',
        'net_sales',
        'shift',
        'notes',
    ];
}
