<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomRating extends Model
{
    use HasFactory;

    protected $table = 'room_ratings';

    protected $fillable = [
        'room_number',
        'customer_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Relationship: Rating belongs to a room.
     */
    public function room()
    {
        return $this->belongsTo(RoomInfo::class, 'room_number', 'room_number');
    }

    /**
     * Relationship: Rating belongs to a customer.
     */
    public function customer()
    {
        return $this->belongsTo(AccountCustomer::class, 'customer_id', 'customer_id');
    }

    /**
     * Accessor: Display stars as icons or formatted rating.
     */
    public function getStarsAttribute(): string
    {
        return str_repeat('⭐', $this->rating);
    }

    /**
     * Scope: Filter ratings for a specific room.
     */
    public function scopeForRoom($query, string $roomNumber)
    {
        return $query->where('room_number', $roomNumber);
    }

    /**
     * Scope: Filter ratings for a specific customer.
     */
    public function scopeByCustomer($query, string $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
