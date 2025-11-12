<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RestoOrder;

class RestoMenu extends Model
{
    use HasFactory;

    protected $table = 'resto_menu';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'menu_id',
        'name',
        'description',
        'price',
        'category',
        'stock_quantity',
        'is_available',
        'number_of_orders',
        'main_ingredients',
        'allergens',
        'cost_price',
        'production_cost',
        'image_url',
    ];

    protected $casts = [
        'main_ingredients' => 'array',
        'allergens' => 'array',
        'number_of_orders' => 'array',     // [{"date":"Y-m-d","orders":number}]
        'production_cost' => 'array',      // {"cost":amount,"per":number_of_orders}
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    /**
     * Computed Attributes
     */
    public function getProfitAttribute(): ?float
    {
        if ($this->cost_price && $this->price) {
            return round($this->price - $this->cost_price, 2);
        }
        return null;
    }

    public function getAvailabilityStatusAttribute(): string
    {
        return $this->is_available ? 'Available' : 'Unavailable';
    }

    /**
     * Auto-update availability based on stock.
     */
    protected static function booted(): void
    {
        static::saving(function ($menu) {
            $menu->is_available = $menu->stock_quantity > 0;
        });
    }

    /**
     * Relationships
     */
    public function orders()
    {
        return $this->belongsToMany(RestoOrder::class, 'resto_order_items', 'menu_id', 'order_id')
            ->withPivot(['qty', 'subtotal'])
            ->withTimestamps();
    }
}
