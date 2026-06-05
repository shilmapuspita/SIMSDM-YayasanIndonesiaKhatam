<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /* ================= RELATIONSHIP ================= */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ================= SCOPES ================= */

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /* ================= METHODS ================= */

    /**
     * Get action badge color
     */
    public function getActionBadgeClass()
    {
        return match ($this->action) {
            'login' => 'bg-success',
            'logout' => 'bg-secondary',
            'create_employee' => 'bg-primary',
            'update_employee' => 'bg-info',
            'delete_employee' => 'bg-danger',
            'attendance' => 'bg-warning',
            'leave_request' => 'bg-dark',
            default => 'bg-light text-dark'
        };
    }

    /**
     * Get formatted action name
     */
    public function getActionName()
    {
        return match ($this->action) {
            'login' => 'Login',
            'logout' => 'Logout',
            'create_employee' => 'Tambah Pegawai',
            'update_employee' => 'Edit Pegawai',
            'delete_employee' => 'Hapus Pegawai',
            'attendance' => 'Absensi',
            'leave_request' => 'Pengajuan Cuti',
            default => ucfirst(str_replace('_', ' ', $this->action))
        };
    }
}
