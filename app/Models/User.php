<?php

namespace App\Models;

use App\Models\ActivityLog;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'photo',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'is_online',
        'last_activity_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_online' => 'boolean',
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    /* ================= RELATIONSHIP ================= */

    // 1 user punya 1 data employee
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // user (admin/hrd) menyetujui banyak cuti
    public function approvedLeaves()
    {
        return $this->hasMany(Leave::class, 'approved_by');
    }

    // Activity logs
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    /* ================= METHODS ================= */

    /**
     * Update last login info
     */
    public function updateLastLogin($ip = null, $userAgent = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?: request()->ip(),
            'last_login_user_agent' => $userAgent ?: request()->userAgent(),
            'is_online' => true,
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Update last activity
     */
    public function updateLastActivity()
    {
        $this->update([
            'last_activity_at' => now(),
            'is_online' => true,
        ]);
    }

    /**
     * Mark as offline
     */
    public function markAsOffline()
    {
        $this->update([
            'is_online' => false,
        ]);
    }

    /**
     * Log user activity
     */
    public function logActivity($action, $description, $metadata = null)
    {
        return $this->activityLogs()->create([
            'activity' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Check if user is online (active within last 5 minutes)
     */
    public function isCurrentlyOnline()
    {
        return $this->is_online && $this->last_activity_at &&
            $this->last_activity_at->diffInMinutes(now()) <= 5;
    }

    /**
     * Get role badge color
     */
    public function getRoleBadgeClass()
    {
        return match ($this->role) {
            'admin' => 'bg-danger',
            'hrd' => 'bg-primary',
            'employee' => 'bg-success',
            default => 'bg-secondary'
        };
    }

    /**
     * Get status badge
     */
    public function getStatusBadge()
    {
        if (!$this->is_active) {
            return '<span class="badge bg-secondary">Nonaktif</span>';
        }

        if ($this->isCurrentlyOnline()) {
            return '<span class="badge bg-success">Online</span>';
        }

        return '<span class="badge bg-warning">Offline</span>';
    }
}
