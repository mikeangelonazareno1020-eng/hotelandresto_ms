<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogsCustomer extends Model
{
    use HasFactory;

    protected $table = 'logs_customer';

    protected $fillable = [
        'customer_id',
        'log_type',
        'action',
        'message',
        'ip_address',
        'device',
        'location',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * 🔗 Relation to Customer
     */
    public function customer()
    {
        return $this->belongsTo(AccountCustomer::class, 'customer_id', 'customer_id');
    }

    /**
     * 🧾 Helper: Create a quick log entry.
     * Example:
     * ```php
     * LogsCustomer::record($customerId, 'Reservation', 'Created Booking', 'Booked room 101', ['reservation_id' => 'HRES100001']);
     * ```
     */
    public static function record($customerId, $type, $action, $message = null, $metadata = [])
    {
        return self::create([
            'customer_id' => $customerId,
            'log_type' => $type,
            'action' => $action,
            'message' => $message,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'device' => request()->header('User-Agent'),
        ]);
    }
}
