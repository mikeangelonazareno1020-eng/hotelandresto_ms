<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationCustomer extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'notifications_customer';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'customer_id',
        'title',
        'message',
        'type',
        'is_read',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Relationship: Notification belongs to a customer.
     */
    public function customer()
    {
        return $this->belongsTo(AccountCustomer::class, 'customer_id', 'customer_id');
    }

    /**
     * Scope: Retrieve only unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Accessor for a formatted label based on notification type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'booking' => '🏨 Booking Update',
            'order' => '🍽️ Restaurant Order',
            'system' => '⚙️ System Notice',
            'promo' => '🎉 Promotion',
            default => ucfirst($this->type ?? 'General'),
        };
    }
}
