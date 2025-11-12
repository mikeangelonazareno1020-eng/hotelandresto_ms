<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogsReport extends Model
{
    protected $table = 'logs_reports';
    protected $fillable = [
        'admin_id','admin_name','role','type','report_type','reference_id','amount','payment_method',
        'transaction_status','description','ip_address','device','browser','location','logged_at',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'logged_at' => 'datetime',
    ];

    // Backward-compat properties used by old views
    public function getReportDateAttribute()
    {
        return optional($this->logged_at)->toDateString();
    }
    public function getCashierNameAttribute()
    {
        return $this->admin_name;
    }
}
