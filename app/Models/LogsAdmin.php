<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogsAdmin extends Model
{
    protected $table = 'logs_admin';
    protected $fillable = [
        'admin_id','admin_name','role','type','action_type','reference_id','description','log_type',
        'ip_address','device','browser','location','logged_at',
    ];
    protected $casts = [
        'logged_at' => 'datetime',
    ];

    // Backward-compat for blades expecting cashier_name
    public function getCashierNameAttribute()
    {
        return $this->admin_name;
    }
}
