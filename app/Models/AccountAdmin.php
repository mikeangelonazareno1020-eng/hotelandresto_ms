<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class AccountAdmin extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'account_admins';

    protected $fillable = [
        // 🆔 Identifiers
        'admin_id',

        // 👤 Basic Information
        'name',
        'email',
        'password',
        'phone',
        'address',

        // 📋 Profile Info
        'gender',
        'birthdate',
        'profile_image',

        // 🛡️ Role & Status
        'role',
        'is_active',

        // 🔐 Security & Login Tracking
        'last_login_at',
        'last_login_ip',
        'two_factor_secret',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'birthdate' => 'date',
        'last_login_at' => 'datetime',
    ];

    /**
     * Automatically hash password when setting.
     */
    public function setPasswordAttribute($value)
    {
        if ($value && !str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    /**
     * Accessor for formatted admin ID (e.g., ADM-10001).
     */
    public function getFormattedIdAttribute(): string
    {
        return $this->admin_id ?? 'ADM-?????';
    }

    /**
     * Scope: Only active admins.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by role.
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
