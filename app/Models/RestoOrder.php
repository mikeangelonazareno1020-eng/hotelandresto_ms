<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\AccountAdmin;
use App\Models\RestoMenu;

class RestoOrder extends Model
{
    use HasFactory;

    protected $table = 'resto_orders';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'daily_order_number',
        'order_id',
        'order_type',
        'admin_id',
        'cashier_name',
        'main_dish',
        'dessert',
        'drinks',
        'rice',
        'appetizer',
        'combo',
        'special_request',
        'total_amount',
        'status',
        'cancel_reason',
        'ordered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'main_dish' => 'array',
        'dessert' => 'array',
        'drinks' => 'array',
        'rice' => 'array',
        'appetizer' => 'array',
        'combo' => 'array',
        'total_amount' => 'decimal:2',
        'ordered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Auto-generate daily order number and readable order ID
     */
    protected static function booted(): void
    {
        static::creating(function ($order) {
            $latestNumber = DB::table('resto_orders')
                ->whereDate('ordered_at', now('Asia/Manila')->toDateString())
                ->max('daily_order_number');

            $order->daily_order_number = $latestNumber ? $latestNumber + 1 : 1;

            $order->order_id = 'OR-' . now('Asia/Manila')->format('Ymd') . '-' .
                str_pad($order->daily_order_number, 4, '0', STR_PAD_LEFT);

            $order->ordered_at = now('Asia/Manila');
            $order->status = $order->status ?? 'Pending';
        });

        static::updating(function ($order) {
            if ($order->isDirty('status') && $order->status === 'Cancelled') {
                $order->cancelled_at = now('Asia/Manila');
            }
        });
    }

    /**
     * Accessors
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount ?? 0, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'Pending' => '🕒 Pending',
            'Preparing' => '🍳 Preparing',
            'Served' => '✅ Served',
            'Cancelled' => '❌ Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Relationships
     */
    public function admin()
    {
        return $this->belongsTo(AccountAdmin::class, 'admin_id', 'admin_id');
    }

    public function menus()
    {
        return $this->belongsToMany(RestoMenu::class, 'resto_order_items', 'order_id', 'menu_id')
            ->withPivot(['qty', 'subtotal'])
            ->withTimestamps();
    }

    /**
     * Query Scopes
     */
    public function scopeToday($query)
    {
        return $query->whereDate('ordered_at', now('Asia/Manila')->toDateString());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopePreparing($query)
    {
        return $query->where('status', 'Preparing');
    }

    public function scopeServed($query)
    {
        return $query->where('status', 'Served');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'Cancelled');
    }
}
