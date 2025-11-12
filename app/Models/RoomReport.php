<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomReport extends Model
{
    use HasFactory;

    protected $table = 'room_reports';

    protected $fillable = [
        'reservation_id',
        'customer_id',
        'room_number',
        'report_type',
        'report',
    ];

    protected $casts = [
        'report' => 'string',
    ];

    /**
     * Relationship: Report belongs to a room.
     */
    public function room()
    {
        return $this->belongsTo(RoomInfo::class, 'room_number', 'room_number');
    }

    /**
     * Relationship: Report belongs to a customer.
     */
    public function customer()
    {
        return $this->belongsTo(AccountCustomer::class, 'customer_id', 'customer_id');
    }

    /**
     * Relationship: Report belongs to a reservation.
     */
    public function reservation()
    {
        return $this->belongsTo(RoomReservation::class, 'reservation_id', 'reservation_id');
    }

    /**
     * Scope: Filter by report type.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('report_type', $type);
    }

    /**
     * Accessor: Get a readable label for report type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->report_type) {
            'Plumbing' => '🚰 Plumbing Issue',
            'Electrical' => '💡 Electrical Problem',
            'HVAC' => '❄️ Air Conditioning / Heating',
            'Other' => '🛠 Other Report',
            default => ucfirst($this->report_type),
        };
    }
}
