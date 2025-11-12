<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomReservation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'room_reservations';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'reservation_id',
        'room_number',
        'reservation_process',
        'customer_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'address',
        'booking_for',
        'guest_name',
        'guest_quantity',
        'checkin_date',
        'checkout_date',
        'total_days',
        'actual_checkin',
        'actual_checkout',
        'acquired_days',
        'added_amenities', // ✅ corrected from room_added_amenities
        'extras',
        'special_request',
        'arrival_time',
        'room_charge',
        'amenities_charge',
        'extra_charge',
        'transport_charge',
        'total_amount',
        'net_amount',
        'payment_status',
        'reservation_status',
        'cancel_date',
        'cancel_list',
        'cancel_reason',
        'is_new',
    ];

    /**
     * Attribute casting for JSON, datetime, and decimal fields.
     */
    protected $casts = [
        // 📅 Date and Time
        'checkin_date' => 'date',
        'checkout_date' => 'date',
        'actual_checkin' => 'datetime',
        'actual_checkout' => 'datetime',
        'cancel_date' => 'datetime',

        // 🧾 JSON / Array
        'added_amenities' => 'array',
        'extras' => 'array',
        'net_amount' => 'array',
        'cancel_list' => 'array',

        // 💰 Decimal Precision
        'room_charge' => 'decimal:2',
        'amenities_charge' => 'decimal:2',
        'extra_charge' => 'decimal:2',
        'transport_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',

        // 🆕 Boolean
        'is_new' => 'boolean',
    ];

    /**
     * Automatically generate a formatted reservation ID (e.g., RES-100001).
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reservation_id)) {
                $latestId = static::max('id') ?? 0;
                $model->reservation_id = 'RES-' . str_pad($latestId + 100001, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Accessor for full name of the guest.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    /**
     * Relationship: Reservation belongs to a customer.
     */
    public function customer()
    {
        return $this->belongsTo(AccountCustomer::class, 'customer_id', 'customer_id');
    }

    /**
     * Relationship: Reservation is linked to a room.
     */
    public function room()
    {
        return $this->belongsTo(RoomInfo::class, 'room_number', 'room_number');
    }

    /**
     * Computed attribute: Check if the reservation is currently active.
     */
    public function getIsActiveAttribute(): bool
    {
        return in_array($this->reservation_status, ['Booked', 'Checked In']);
    }
}
