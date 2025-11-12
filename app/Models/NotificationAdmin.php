<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationAdmin extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'notifications_admin';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'admin_id',
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
     * Relationship: Notification belongs to an admin.
     */
    public function admin()
    {
        return $this->belongsTo(AccountAdmin::class, 'admin_id', 'adminId');
    }

    /**
     * Scope: Get only unread notifications.
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
     * Accessor for formatted type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'booking' => '🛎️ Booking',
            'order' => '🍽️ Restaurant Order',
            'system' => '⚙️ System Alert',
            default => ucfirst($this->type ?? 'General'),
        };
    }
}
