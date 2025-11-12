<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\AccountAdmin;
use App\Models\RoomReservation;
use App\Models\RestoOrder;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'receipts';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'receipt_id',
        'reference_id',
        'type',
        'customer_id',
        'customer_name',
        'email',
        'phone',
        'items',
        'subtotal',
        'total',
        'amount',
        'amount_tendered',
        'change_due',
        'payment_method',
        'payment_details',
        'issued_by',
        'admin_id',
        'issued_at',
    ];

    protected $casts = [
        'items' => 'array',
        'payment_details' => 'array',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'amount' => 'decimal:2',
        'amount_tendered' => 'decimal:2',
        'change_due' => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    protected $attributes = [
        'payment_details' => '{"reference_number": null, "transaction_id": null, "amount": 0.00, "screenshot": null}',
    ];

    /**
     * Auto-generate unique receipt_id (e.g. RCPT-10001)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->receipt_id)) {
                $latestId = static::max('id') ?? 0;
                $model->receipt_id = 'RCPT-' . str_pad($latestId + 10001, 5, '0', STR_PAD_LEFT);
            }

            if (empty($model->issued_at)) {
                $model->issued_at = now('Asia/Manila');
            }
        });
    }

    /**
     * 🔗 Relationships
     */
    public function admin()
    {
        return $this->belongsTo(AccountAdmin::class, 'admin_id', 'id');
    }

    public function roomReservation()
    {
        return $this->belongsTo(RoomReservation::class, 'reference_id', 'reservation_id');
    }

    public function restoOrder()
    {
        return $this->belongsTo(RestoOrder::class, 'reference_id', 'order_id');
    }

    /**
     * 💰 Accessor for formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return '₱' . number_format($this->total, 2);
    }

    /**
     * 📘 Scopes
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeIssuedBetween($query, $start, $end)
    {
        return $query->whereBetween('issued_at', [$start, $end]);
    }
}
