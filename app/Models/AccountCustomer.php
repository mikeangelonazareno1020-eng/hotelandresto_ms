<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountCustomer extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'account_customers';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // 🧾 Identifiers
        'customer_id',

        // 🧍 Personal Information
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'customer_password',
        'phone',
        'birthdate',
        'gender',
        'profile_image',

        // 📍 Address Information
        'region',
        'province',
        'city',
        'barangay',
        'street',
        'other_address',

        // ✅ Optional future field (uncomment if added to migration)
        // 'is_new',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'birthdate' => 'date',
        // 'is_new' => 'boolean', // Uncomment if added to schema
    ];

    /**
     * Automatically hash password before saving.
     */
    public function setCustomerPasswordAttribute($value)
    {
        if ($value && !str_starts_with($value, '$2y$')) {
            $this->attributes['customer_password'] = bcrypt($value);
        }
    }

    /**
     * Automatically generate a unique customer_id (e.g., HC100001).
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->customer_id)) {
                $latestId = static::max('id') ?? 0;
                $model->customer_id = 'HC' . str_pad($latestId + 100001, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Accessor for full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
